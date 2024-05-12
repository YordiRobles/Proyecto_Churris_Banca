<link href="/css/followuser.css" rel="stylesheet">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show User</title>
</head>
<body>
    @if (isset($name))
        <h1>User Name: {{ $name }}</h1>
    @endif

    @if (isset($email))
        <p>Email: {{ $email }}</p>
    @endif

    <form action="{{ route('user.follow', ['name' => $name]) }}" method="POST">
        @csrf
        <input type="hidden" name="action" id="followAction" value="follow">
        <button type="button" id="followButton">Empezar a seguir</button>
    </form>
    <script src="{{ asset('/js/followbutton.js') }}" defer></script>

</body>
</html>
