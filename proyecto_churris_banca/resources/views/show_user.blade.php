<link href="/css/followuser.css" rel="stylesheet">
<!DOCTYPE html>
<html lang="en">
    <x-app-layout>
        <x-slot name="header">
            <div class="header-container">
                <h2 class="font-title">
                    {{ __('Red Social') }}
                </h2>
                <div class="search-container">
                    <form action="{{ route('search.users') }}" method="GET">
                        <div class="search-box">
                            <input type="text" name="query" placeholder="Buscar usuario por nombre" class="search-input">
                            <button type="submit" class="search-button"></button>
                        </div>
                    </form>
                </div>
            </div>
        </x-slot>
        <body>
            @if (isset($name))
            <div class="title-container">
                <h2 class="title-result">{{ $name }}</h2>
             </div>
            @endif
            @if (isset($email))
                <p>Email: {{ $email }}</p>
            @endif

            <form id="followForm" action="{{ route('user.follow', ['name' => $name]) }}" method="POST">
            @csrf
                <div class="follow-buttons">
                    <button type="submit" name="follow" class="buttonfollow" value="1" id="followButton">
                    <span>Empezar a seguir</span>
                    </button>
                    <button type="submit" class= "buttonunfollow "name="unfollow" value="2" id="unfollowButton">
                    <span>Dejar de seguir</span>
                    </button>
                </div>
            </form>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script>
                var isFollowing = {{ json_encode($is_following) }};
            </script>
            <script src="{{ asset('/js/followbutton.js') }}" defer></script>
        </body>
    </x-app-layout>
</html>
