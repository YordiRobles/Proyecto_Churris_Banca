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

class BankingNetController extends Controller
{
    public function storeTransaction(Request $request)
    {
        return $this->verifyTransaction($request);
    }

    public function verifyTransaction(Request $request)
    {
        $currentUser = Auth::user();
        $request->validate([
            'username' => 'required|string',
            'amount' => 'required|integer|min:1',
            'transfer-key' => 'required|file',
        ]);
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
        
        $transactionData = json_encode([
            'username' => $recipientName,
            'amount' => $request->input('amount'),
        ]);

        $signature = $this->signData($transactionData, $privateKeyContent);
        if ($signature === false) {
            return redirect()->back()->with('failed', 'No se pudo firmar los datos.');
        }

        $isValid = $this->verifySignature($transactionData, $signature, $publicKey);
        if ($isValid) {
            // Realizar la transferencia utilizando la función transfer adaptada
            return $this->transfer($request, $currentUser->name, $recipientName, $request->input('amount'));
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
        $username = Auth::user()->name;

        // Hacer la solicitud HTTP al CGI
        $caCertPath = env('CA_CERT_PATH');
        $response = Http::withOptions(['verify' => $caCertPath])->get('https://cgiequipo04/cgi-bin/getBalance', [
            'name' => $username
        ]);

        // Verificar que la solicitud fue exitosa
        if ($response->successful()) {
            // Parsear el HTML de la respuesta
            $html = $response->body();
            $crawler = new Crawler($html);
            $name = $crawler->filter('table tr td')->eq(0)->text();
            $balance = $crawler->filter('table tr td')->eq(1)->text();

            // Pasar los datos a la vista
            return view('banking_net', [
                'username' => $name,
                'balance' => $balance
            ]);
        }

        return redirect()->back()->with('failed', 'No se pudo obtener el balance.');
    }

    public function transfer(Request $request, $from, $to, $amount)
    {
        // Hacer la solicitud HTTP al CGI para la transferencia de fondos
        $caCertPath = env('CA_CERT_PATH');
        $response = Http::withOptions(['verify' => $caCertPath])->get('https://cgiequipo04/cgi-bin/balanceTransferLogs', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
        ]);

        /*$response = Http::get('https://cgiequipo04/cgi-bin/getBalance', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
        ]);*/

        // Verificar que la solicitud fue exitosa
        if ($response->successful()) {
            // Parsear el HTML de la respuesta
            $html = $response->body();

            // Almacenar el resultado en la sesión y redirigir de vuelta a la vista con el resultado
            return redirect()->route('banking.net')->with('success', 'Se ha realizado la transacción.');
        }

        return redirect()->back()->with('failed', 'No se pudo completar la transacción.');
    }

    /*public function showBankingNet()
    {
        $fixedUsername = 'Jason Murillo Madrigal';

        // Hacer la solicitud HTTP al CGI
        $response = Http::get('http://172.24.131.196/cgi-bin/getBalance', [
            'name' => $fixedUsername
        ]);

        // Verificar que la solicitud fue exitosa
        if ($response->successful()) {
            // Parsear el HTML de la respuesta
            $html = $response->body();
            $crawler = new Crawler($html);
            
            $name = $crawler->filter('table tr td')->eq(0)->text();
            $balance = $crawler->filter('table tr td')->eq(1)->text();

            // Pasar los datos a la vista
            return view('banking_net', [
                'username' => $name,
                'balance' => $balance
            ]);
        }

        return redirect()->back()->with('failed', 'No se pudo obtener el balance.');
    }*/
}