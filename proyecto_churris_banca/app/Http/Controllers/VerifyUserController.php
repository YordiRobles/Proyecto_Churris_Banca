<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Openssl;

class VerifyUserController extends Controller
{

    public function DisplayView()
    {
        return view('verify_user');
    }

    public function verifyPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'max:40', 'regex:/^[^\'";*(){}[\]<>\\&$]+$/'],
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->with('failed', 'La contraseña tiene datos invalidos');
        }
        
        if (Hash::check($request->password, Auth::user()->password)) {
            $request->session()->put('user_verified', true);
            return $this->verifyUserCertificate();
        } else {
            return redirect()->back()->with('failed', 'La contraseña no coincide');
        }
    }

    public function verifyUserCertificate()
    {
        $caCertPath = env('CA_CERT_PATH');
        $caCert = file_get_contents($caCertPath);
        if ($caCert === false) {
            return redirect()->back()->with('failed', 'No se encontró el certificado del CA');
        }

        $currentUser = Auth::user();

        $certificatesDir = env('USER_CERTS_PATH');

        $certFilename = $currentUser->name . '.crt';

        $userCertPath = $certificatesDir . DIRECTORY_SEPARATOR . $certFilename;

        if (!file_exists($userCertPath)) {
            return redirect()->back()->with('failed', 'No existe la ruta del certificado');
        }

        $userCert = file_get_contents($userCertPath);
        if ($userCert === false) {
            return redirect()->back()->with('failed', 'No se pudo leer el certificado del usuario');
        }

        $caCertResource = openssl_x509_read($caCert);
        if ($caCertResource === false) {
            return redirect()->back()->with('failed', 'El certificado del CA no es válido');
        }

        $userCertResource = openssl_x509_read($userCert);
        if ($userCertResource === false) {
            return redirect()->back()->with('failed', 'El certificado del usuario no es válido');
        }

        $valid = openssl_x509_verify($userCert, $caCert);

        if ($valid === 1) {
            return redirect()->route('banking.net');
        } elseif ($valid === 0) {
            return redirect()->back()->with('failed', 'El certificado del usuario no fue firmado por la CA');
        } else {
            return redirect()->back()->with('failed', 'Error al verificar el certificado del usuario');
        }
    }
}