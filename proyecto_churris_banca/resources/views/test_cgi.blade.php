<!DOCTYPE html>
<html>
<head>
    <title>Pruebas CGI</title>
    <link href="/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="header-container">
        <h2 class="font-title">Pruebas CGI</h2>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="bankingnet-title">Resultado del CGI</h1>
                    <p>Nombre de usuario: {{ $username }}</p>
                    <p>Balance: ${{ number_format($balance, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="bankingnet-title">Transferencia de Fondos</h1>
                    <form action="{{ route('test.cgi.transfer') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="from">De:</label>
                            <input type="text" id="from" name="from" required value="{{ $username }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="to">Para:</label>
                            <input type="text" id="to" name="to" required>
                        </div>
                        <div class="form-group">
                            <label for="amount">Monto:</label>
                            <input type="number" id="amount" name="amount" required min="1">
                        </div>
                        <button type="submit" class="submit-button">Realizar Transferencia</button>
                    </form>
                    @if(session('failed'))
                        <div id="failed-alert" class="alert alert-danger">
                            {{ session('failed') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @if(isset($result))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="bankingnet-title">Resultado de la Transferencia</h1>
                    {!! $result !!}
                </div>
            </div>
        </div>
        @endif
    </div>
</body>
</html>