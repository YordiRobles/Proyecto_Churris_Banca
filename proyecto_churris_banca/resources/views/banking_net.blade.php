<link href="/css/dashboard.css" rel="stylesheet">
<link href="/css/bankingnet.css" rel="stylesheet">
<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="font-title">
                {{ __('Red Bancaria') }}
            </h2>
            <div class="user-balance">
                <span>{{ $username }}</span>
                <span>Balance: {{ number_format($balance, 2) }} {{ $currency }}</span>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="transaction-form-container">
            <form class="transaction-form" action="{{ route('banking.transaction') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h1 class="bankingnet-title">Transferencia de Fondos</h1>
                <input type="text" name="username" placeholder="Nombre del destinatario" class="transaction-input" required>
                <input type="number" name="amount" placeholder="Monto a transferir" class="transaction-input" required min="1">
                <input type="file" name="transfer-key" accept=".key" class="transaction-file-input" required>
                <button type="submit" class="transaction-button">Realizar transferencia</button>
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
            </form>
        </div>
        <hr class="divisor-line">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="transfer-history">
                        <h1 class="bankingnet-title">Historial de Transferencias</h1>
                        <div id="transaction-container" class="transfer-list">
                            <!-- Mostrar las transacciones reales obtenidas del CGI -->
                            @php
                                $allTransactions = array_merge(
                                    array_map(function($transaction) {
                                        $transaction['type'] = 'sent';
                                        return $transaction;
                                    }, $transactions['sent'] ?? []),
                                    array_map(function($transaction) {
                                        $transaction['type'] = 'received';
                                        return $transaction;
                                    }, $transactions['received'] ?? [])
                                );

                                usort($allTransactions, function($a, $b) {
                                    return strtotime($b['date']) - strtotime($a['date']);
                                });
                            @endphp

                            @foreach($allTransactions as $transaction)
                                <div class="transaction">
                                    <div class="transaction-header">
                                        <div class="transaction-info">
                                            @if($transaction['type'] == 'sent')
                                                <h3>Enviado a: {{ $transaction['recipient'] }}</h3>
                                                <p>Monto enviado: {{ number_format($transaction['amount_sent'], 2) }} {{ $transaction['sender_currency'] }}</p>
                                                <p>Monto recibido: {{ number_format($transaction['amount_received'], 2) }} {{ $transaction['recipient_currency'] }}</p>
                                            @else
                                                <h3>Recibido de: {{ $transaction['sender'] }}</h3>
                                                <p>Monto recibido: {{ number_format($transaction['amount_received'], 2) }} {{ $transaction['recipient_currency'] }}</p>
                                                <p>Monto enviado: {{ number_format($transaction['amount_sent'], 2) }} {{ $transaction['sender_currency'] }}</p>
                                            @endif
                                            <p>Fecha: {{ $transaction['date'] }}</p>
                                        </div>
                                        <div class="transaction-icon">
                                            @if($transaction['type'] == 'sent')
                                                <img src="img/red.jpeg" alt="Icono de enviado" class="transaction-status-icon">
                                            @else
                                                <img src="img/green.jpeg" alt="Icono de recibido" class="transaction-status-icon">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pagination">
            <button id="prev-page" class="page-button" disabled>Anterior</button>
            <button id="next-page" class="page-button">Siguiente</button>
        </div>
    </div>
</x-app-layout>

<script src="{{ asset('js/messages.js') }}"></script>
<script src="{{ asset('js/banking_pagination.js') }}"></script>

