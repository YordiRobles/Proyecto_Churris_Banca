<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class TestCGIController extends Controller
{
    public function show(Request $request)
    {
        $fixedUsername = 'Jason Murillo Madrigal';

        // Hacer la solicitud HTTP al CGI para obtener el balance
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
            return view('test_cgi', [
                'username' => $name,
                'balance' => $balance,
                'result' => session('result') ?? null
            ]);
        }

        return redirect()->back()->with('failed', 'No se pudo obtener el balance.');
    }

    public function transfer(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $amount = $request->input('amount');

        // Hacer la solicitud HTTP al CGI para la transferencia de fondos
        $response = Http::get('http://172.24.131.196/cgi-bin/balance_transfer', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
        ]);

        // Verificar que la solicitud fue exitosa
        if ($response->successful()) {
            // Parsear el HTML de la respuesta
            $html = $response->body();

            // Almacenar el resultado en la sesión y redirigir de vuelta a la vista con el resultado
            return redirect()->route('test.cgi')->with('result', $html);
        }

        return redirect()->back()->with('failed', 'No se pudo completar la transacción.');
    }
}