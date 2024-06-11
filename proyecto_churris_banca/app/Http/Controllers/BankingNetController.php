<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Openssl;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Validator;

class BankingNetController extends Controller
{
    public function storeTransaction(Request $request)
    {
        return $this->verifyTransaction($request);
    }

    public function verifyTransaction(Request $request)
    {
        $currentUser = Auth::user();
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:40', 'regex:/^[a-zA-Z.]+$/'],
            'amount' => 'required|integer|min:1',
            'transfer-key' => 'required|file',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', 'El nombre de usuario al que se le va a realizar la transacción no es válido.');
        }

        $recipientName = $request->input('username');
        $recipient = User::where('name', $recipientName)->first();

        if (!$recipient || $recipientName === $currentUser->name) {
            return redirect()->back()->with('failed', 'No se puede realizar una transacción a este usuario.');
        }

        $privateKeyFile = $request->file('transfer-key');
        $privateKeyContent = file_get_contents($privateKeyFile->getRealPath());

        $userCertificatesDir = env('USER_CERTS_PATH');

        $certFilename = $currentUser->name . '.crt';

        $userCertPath = $userCertificatesDir . DIRECTORY_SEPARATOR . $certFilename;

        if (!file_exists($userCertPath)) {
            return redirect()->back()->with('failed', 'No se encontró el certificado del usuario.');
        }

        $crtContent = file_get_contents($userCertPath);

        $publicKey = $this->getPublicKeyFromCRT($crtContent);
        if ($publicKey === false) {
            return redirect()->back()->with('failed', 'No se pudo extraer la clave pública del certificado.');
        }

        $certResource = openssl_x509_read($crtContent);
        $privateKeyResource = openssl_pkey_get_private($privateKeyContent);
        $isKeyValid = openssl_x509_check_private_key($certResource, $privateKeyResource);

        if (!$isKeyValid) {
            return redirect()->back()->with('failed', 'La clave privada no corresponde al certificado.');
        }
                  
        $dataTransaction = urlencode($currentUser->name) . urlencode($recipientName) . urlencode($request->input('amount'));
        $signature = $this->signData($dataTransaction, $privateKeyContent);

        if ($signature === false) {
            return redirect()->back()->with('failed', 'No se pudo firmar los datos.');
        }

        $isValid = $this->verifySignature($dataTransaction, $signature, $publicKey);
        if ($isValid) {
            return $this->transfer($request, $currentUser->name, $recipientName, $request->input('amount'), $signature);
        } else {
            return redirect()->route('banking.net')->with('failed', 'La firma de la transacción no es válida.');
        }
    }

    private function signData($data, $privateKeyContent)
    {
        $privateKey = openssl_pkey_get_private($privateKeyContent);
        if ($privateKey === false) {
            return false;
        }

        $signature = '';
        $success = openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKey);

        if (!$success) {
            return false;
        }

        return base64_encode($signature);
    }

    private function getPublicKeyFromCRT($crtContent)
    {
        $cert = openssl_x509_read($crtContent);
        if ($cert === false) {
            return false;
        }

        $publicKey = openssl_pkey_get_details(openssl_pkey_get_public($cert));
        if ($publicKey === false) {
            return false;
        }

        return $publicKey['key'];
    }

    private function verifySignature($data, $signature, $publicKey)
    {
        $signature = base64_decode($signature);
        $publicKeyResource = openssl_pkey_get_public($publicKey);

        if ($publicKeyResource === false) {
            return false;
        }

        $result = openssl_verify($data, $signature, $publicKeyResource, OPENSSL_ALGO_SHA256);
        openssl_free_key($publicKeyResource);
        return $result === 1;
    }

    public function getBalance(Request $request)
    {
        Log::info('getBalance method called');

        $username = Auth::user()->name;

        $caCertPath = env('CA_CERT_PATH');
        $response = Http::withOptions(['verify' => $caCertPath])->get('https://cgiequipo04/cgi-bin/getBalanceEnv', [
            'name' => $username
        ]);

        if ($response->successful()) {
            $html = $response->body();
            $crawler = new Crawler($html);
            $name = $crawler->filter('table tr td')->eq(0)->text();
            $balance = $crawler->filter('table tr td')->eq(1)->text();

            $transactions = $this->getTransactionLogs($username);

            return view('banking_net', [
                'username' => $name,
                'balance' => $balance,
                'transactions' => $transactions
            ]);
        }

        return redirect()->back()->with('failed', 'No se pudo obtener el balance.');
    }

    public function transfer(Request $request, $from, $to, $amount, $signature)
    {
        $username = Auth::user()->name;
        $caCertPath = env('CA_CERT_PATH');
        $response = Http::withOptions(['verify' => $caCertPath])->get('https://cgiequipo04/cgi-bin/getBalanceEnv', [
            'name' => $username
        ]);
        if ($response->successful()) {
            $html = $response->body();
            $crawler = new Crawler($html);
            $balance = $crawler->filter('table tr td')->eq(1)->text();
            if ($balance < $amount) {
                return redirect()->back()->with('failed', 'No se pudo completar la transacción.');
            }
            $response = Http::withOptions(['verify' => $caCertPath])->get('https://cgiequipo04/cgi-bin/balanceTransferLogsCif', [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'signature' => $signature
            ]);
            if ($response->successful()) {
                $html = $response->body();
                return redirect()->route('banking.net')->with('success', 'Se ha realizado la transacción.');
            }
        }
    }

    private function getTransactionLogs($username)
    {
        $caCertPath = env('CA_CERT_PATH');
        $response = Http::withOptions(['verify' => $caCertPath])->get('https://cgiequipo04/cgi-bin/getLogsEnv', [
            'user' => $username
        ]);

        if ($response->successful()) {
            $html = $response->body();

            Log::info('HTML response from getLogsEnv:', ['html' => $html]);

            return $this->parseTransactions($html);
        }

        return ['sent' => [], 'received' => []];
    }

    private function parseTransactions($html)
    {
        $crawler = new Crawler($html);
        $transactions = [
            'sent' => [],
            'received' => []
        ];

        $sentRows = $crawler->filter('h2:contains("Transacciones enviadas") + table tr');
        foreach ($sentRows as $row) {
            $cells = (new Crawler($row))->filter('td');
            if ($cells->count() == 3) {
                $transactions['sent'][] = [
                    'date' => $cells->eq(0)->text(),
                    'amount' => $cells->eq(1)->text(),
                    'recipient' => $cells->eq(2)->text(),
                ];
            }
        }

        $receivedRows = $crawler->filter('h2:contains("Transacciones recibidas") + table tr');
        foreach ($receivedRows as $row) {
            $cells = (new Crawler($row))->filter('td');
            if ($cells->count() == 3) {
                $transactions['received'][] = [
                    'date' => $cells->eq(0)->text(),
                    'amount' => $cells->eq(1)->text(),
                    'sender' => $cells->eq(2)->text(),
                ];
            }
        }

        usort($transactions['sent'], function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        usort($transactions['received'], function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Depurar los datos
        Log::info('Parsed transactions:', $transactions);

        return $transactions;
    }
}