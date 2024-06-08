<!DOCTYPE html>
<html>
<head>
    <title>Balance del Usuario</title>
    <link href="/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="header-container">
        <h2 class="font-title">Balance del Usuario</h2>
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
    </div>
</body>
</html>