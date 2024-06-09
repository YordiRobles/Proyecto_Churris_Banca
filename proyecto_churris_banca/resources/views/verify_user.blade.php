<link href="/css/dashboard.css" rel="stylesheet">
<link href="/css/login.css" rel="stylesheet">

<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="font-title">
                {{ __('Red Bancaria') }}
            </h2>
        </div>
    </x-slot>
    <div class="container">
        <div class="card">
            <form method="POST" action="{{ route('verify.user.submit') }}">
                @csrf    
                <div>
                    <h1>Ingrese su contraseña</h1>
                </div>
                <div>
                    <label for="password">Contraseña:</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <button type="submit">Continuar</button>
            </form>
            @if(session('success'))
                <div id="success-alert" class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('failed'))
                <div id="failed-alert" class="alert alert-danger">
                    {{ session('failed') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script src="{{ asset('js/messages.js') }}"></script>