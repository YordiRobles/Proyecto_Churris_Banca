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
                <span>Balance: ${{ number_format($balance, 2) }}</span>
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
                        <div class="transfer-list">
                            <!-- Mostrar las transacciones reales obtenidas del CGI -->
                            @if(!empty($transactions['sent']))
                                <h2>Transacciones enviadas:</h2>
                                @foreach($transactions['sent'] as $transaction)
                                    <div class="transaction">
                                        <div class="transaction-header">
                                            <div class="transaction-info">
                                                <h3>Enviado a: {{ $transaction['recipient'] }}</h3>
                                                <p>Fecha: {{ $transaction['date'] }}</p>
                                                <p>Monto enviado: ${{ number_format($transaction['amount'], 2) }}</p>
                                            </div>
                                            <div class="transaction-icon">
                                                <img src="img/churricoin_red.png" alt="Icono de enviado" class="transaction-status-icon">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if(!empty($transactions['received']))
                                <h2>Transacciones recibidas:</h2>
                                @foreach($transactions['received'] as $transaction)
                                    <div class="transaction">
                                        <div class="transaction-header">
                                            <div class="transaction-info">
                                                <h3>Recibido de: {{ $transaction['sender'] }}</h3>
                                                <p>Fecha: {{ $transaction['date'] }}</p>
                                                <p>Monto recibido: ${{ number_format($transaction['amount'], 2) }}</p>
                                            </div>
                                            <div class="transaction-icon">
                                                <img src="img/churricoin_green.png" alt="Icono de recibido" class="transaction-status-icon">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
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

<style>
    .user-balance {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        font-size: 16px;
        color: #000;
        margin-left: auto;
    }
    .user-balance span {
        margin: 2px 0;
    }
</style>