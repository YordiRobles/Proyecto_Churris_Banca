<!-- resources/views/see_profile.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Perfil de {{ $user->name }}</title>
    <link href="/css/dashboard.css" rel="stylesheet">
    <style>
        .profile-header {
            display: flex;
            align-items: center;
        }
        .profile-header img {
            border-radius: 50%;
            margin-right: 20px;
            width: 150px; /* Ajusta el tamaño de la imagen según tus necesidades */
            height: 150px;
            object-fit: cover;
        }
        .profile-header h1 {
            font-size: 2em; /* Tamaño del nombre del usuario */
        }
        .edit-profile-button {
            background-color: black;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
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
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Perfil del Usuario -->
                        <div class="profile-header">
                            <img src="data:image/jpeg;base64,{{ base64_encode($user->image_data) }}" alt="Imagen de {{ $user->name }}">
                            <div>
                                <h1>{{ $user->name }}</h1>
                                <p>Email: {{ $user->email }}</p>
                                <p>Seguidores: {{ $followersCount }}</p>
                                <a href="{{ route('profile.edit', $user->id) }}">
                                    <button class="edit-profile-button">Editar perfil</button>
                                </a>
                            </div>
                        </div>
                        <hr class="divisor-line">
                        <!-- Área donde se muestran las publicaciones -->
                        <div class="post-container">
                            @foreach($user->publications as $publication)
                                <div class="post">
                                    <div class="post-header">
                                    <img src="data:image/jpeg;base64,{{ base64_encode($user->image_data) }}" alt="Imagen de {{ $user->name }}">
                                        <div class="post-info">
                                            <h3>{{ $user->name }}</h3>
                                            <p>{{ $publication->created_at }}</p>
                                        </div>
                                    </div>
                                    <div class="post-content">
                                        <p>{{ $publication->text }}</p>
                                        @if($publication->image_data)
                                        <img src="data:image/jpeg;base64,{{ base64_encode($user->image_data) }}" alt="Imagen de {{ $user->name }}">
                                        @endif
                                    </div>
                                    <div class="post-actions">
                                        <hr>
                                        <button class="like-button">Me gusta</button>
                                        <span class="like-count">{{ $publication->likes_count }}</span>
                                        <button class="dislike-button">No me gusta</button>
                                        <span class="dislike-count">{{ $publication->dislikes_count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>

    <script>
        // Esperar 3 segundos y luego ocultar el mensaje de éxito
        setTimeout(function() {
            document.getElementById('success-alert').style.display = 'none';
        }, 3000); // 3000 milisegundos = 3 segundos
    </script>
</body>
</html>