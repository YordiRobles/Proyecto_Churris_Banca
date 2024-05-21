<!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <link href="/css/login.css" rel="stylesheet">

    </head>
    <body>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="inicio-container">
                <h1 class="inicio-text">Iniciar sesion</h1>
            </div>
        </div>
        <div class="container">
            <div class="card">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                <div>
                    <label for="email">Correo del usuario:</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                    <!-- Password -->
                <div>
                    <label for="password">Contraseña:</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                    <button type="submit">Continuar</button>
                </form>
            </div>
        </div>
    </body>
</html>