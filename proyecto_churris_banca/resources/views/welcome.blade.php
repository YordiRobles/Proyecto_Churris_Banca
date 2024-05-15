<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Churris Banca</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <link href="/css/welcome.css" rel="stylesheet">

    </head>
    <body class="antialiased">
        <div class="relative sm:flex sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
            
            <div class="churrisbanca-title">
                <a href="{{ url('/') }}" class="churrisbanca-button">Churris Banca</a>
            </div>
            @if (Route::has('login'))
                <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="access-buttons">Red Social</a>
                    @else
                        <a href="{{ route('login') }}" class="access-buttons">Iniciar Sesión</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="access-buttons ml-4">Registrarse</a>
                        @endif
                    @endauth
                </div>
            @endif
        
            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <div class="welcome-container">
                    <h1 class="welcome-text">¡Bienvenidos!</h1>
                </div>
                <div class="welcome-image">
                <img src="/img/WelcomeChurrisBanca.jfif" alt="Imagen de Bienvenida a la ChurrisBanca" class="rounded-image">
                </div>
            </div>
        </div>
    </body>
</html>
