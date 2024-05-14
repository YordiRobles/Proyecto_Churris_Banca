<link href="/css/followuser.css" rel="stylesheet">
<!DOCTYPE html>
<html lang="en">

<body>
    @if (isset($name))
        <h1>User Name: {{ $name }}</h1>
    @endif
    @if (isset($email))
        <p>Email: {{ $email }}</p>
    @endif

    <form id="followForm" action="{{ route('user.follow', ['name' => $name]) }}" method="POST">
    @csrf
        <div class="follow-buttons">
            <button type="submit" name="follow" class="buttonfollow" value="1" id="followButton">
            <span>Empezar a eguir</span>
            </button>
            <button type="submit" class= "buttonunfollow "name="unfollow" value="2" id="unfollowButton">
            <span>Dejar de seguir</span>
            </button>
        </div>
    </form>

    <script src="{{ asset('/js/followbutton.js') }}" defer></script>



</body>
</html>
