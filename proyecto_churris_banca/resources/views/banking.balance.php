<link href="/css/dashboard.css" rel="stylesheet">
<link href="/css/bankingnet.css" rel="stylesheet">
<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="font-title">
                {{ __('Red Bancaria') }}
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="balance-info">
            <h1 class="bankingnet-title">Información del Balance</h1>
            <p>Nombre de usuario: {{ $username }}</p>
            <p>Balance: ${{ number_format($balance, 2) }}</p>
        </div>
        <!-- Rest of your view -->
    </div>
</x-app-layout>
<script src="{{ asset('js/messages.js') }}"></script>