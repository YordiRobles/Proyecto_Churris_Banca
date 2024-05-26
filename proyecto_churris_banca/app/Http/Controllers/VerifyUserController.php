<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $request->validate([
            'password' => 'required|string',
        ]);

        // Verificar la contraseña del usuario autenticado
        if (Hash::check($request->password, Auth::user()->password)) {
        // Llamar a la función verifyUserCertificate()
            return $this->verifyUserCertificate();
            //return Redirect::route('bankingnet');
        }

        // Lanza una excepción si la contraseña es incorrecta
        throw ValidationException::withMessages([
            'password' => ['La contraseña es incorrecta.'],
        ]);
    }

    public function verifyUserCertificate()
    {
        // Ruta al certificado del CA (la clave pública del CA)
        $caCertPath = storage_path('CA/rootCACert.crt');

        // Cargar el certificado del CA
        $caCert = file_get_contents($caCertPath);
        if ($caCert === false) {
            return redirect()->back()->with('failed', 'No se encontró el certificado del CA');
        }

        // Buscar el certificado que coincida con el nombre de usuario
        $currentUser = Auth::user();

        // Directorio donde se almacenan los certificados
        $certificatesDir = storage_path('users');

        // Construir el nombre del archivo del certificado
        $certFilename = $currentUser->name . '.crt';

        // Construir la ruta completa al archivo del certificado
        $userCertPath = $certificatesDir . DIRECTORY_SEPARATOR . $certFilename;

        // Verificar si el archivo existe
        if (!file_exists($userCertPath)) {
            Log::log('debug', $userCertPath);
            return redirect()->back()->with('failed', 'No existe la ruta del certificado');
        }

        // Leer el contenido del archivo del certificado
        $userCert = file_get_contents($userCertPath);
        if ($userCert === false) {
            return redirect()->back()->with('failed', 'No se pudo leer el certificado del usuario');
        }

        // Crear un recurso de certificado X.509 a partir del certificado del CA
        $caCertResource = openssl_x509_read($caCert);
        if ($caCertResource === false) {
            return redirect()->back()->with('failed', 'El certificado del CA no es válido');
        }

        // Crear un recurso de certificado X.509 a partir del certificado del usuario
        $userCertResource = openssl_x509_read($userCert);
        if ($userCertResource === false) {
            return redirect()->back()->with('failed', 'El certificado del usuario no es válido');
        }

        // Verificar el certificado del usuario contra el certificado del CA
        $valid = openssl_x509_verify($userCert, $caCert);

        if ($valid === 1) {
            return redirect()->route('bankingnet');
        } elseif ($valid === 0) {
            // El certificado del usuario no fue firmado por el certificado de la CA
            return redirect()->back()->with('failed', 'El certificado del usuario no fue firmado por la CA');
        } else {
            // Hubo un error al verificar el certificado del usuario
            return redirect()->back()->with('failed', 'Error al verificar el certificado del usuario');
        }
    }
}