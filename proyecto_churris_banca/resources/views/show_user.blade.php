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
    <p>asdasdasd </p>

    @if (isset($email))
        <p>Email: {{ $email }}</p>
    @endif

    <form id="followForm" action="{{ route('user.follow', ['name' => $name]) }}" method="POST">
    @csrf
        <div class="follow-buttons">
            <button type="submit" name="follow" value="1" id="followButton">
            <span class="button__text">Seguir usuario</span>
            </button>
            <button type="submit" name="unfollow" value="2" id="unfollowButton">
            <span class="button__text">Dejar de seguir usuario</span>
            </button>
        </div>
    </form>

    <script src="{{ asset('/js/followbutton.js') }}" defer></script>



</body>
</html>
