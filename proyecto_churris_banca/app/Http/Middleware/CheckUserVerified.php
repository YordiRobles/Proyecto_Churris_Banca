<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserVerified
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado y tiene la sesión 'user_verified'
        if (!Auth::check() || !$request->session()->has('user_verified') || $request->session()->get('user_verified') !== true) {
            return redirect()->route('verify.user')->with('failed', 'Por favor verifique su contraseña.');
        }

        return $next($request);
    }
}