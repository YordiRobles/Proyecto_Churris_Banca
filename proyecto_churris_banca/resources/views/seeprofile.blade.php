<!-- resources/views/seeprofile.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Perfil de {{ $user->name }}</title>
</head>
<body>
    <h1>{{ $user->name }}</h1>
    <p>Email: {{ $user->email }}</p>
    <img src="{{ $user->image_data }}" alt="Imagen de {{ $user->name }}">
    <p>Seguidores: {{ $followersCount }}</p>
    
    <a href="{{ route('profile.edit', $user->id) }}">
        <button>Editar perfil</button>
    </a>
</body>
</html>