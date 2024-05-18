<link href="/css/dashboard.css" rel="stylesheet">
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
                    <!-- Formulario para hacer una nueva publicación -->
                    <form class="post-form" action="{{ route('store.post') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="post-inputs">
                            <textarea name="post-content" placeholder="¿Qué estás pensando?" class="post-textarea"></textarea>
                            <input type="file" name="post-image" accept="image/*" class="post-image-input">
                        </div>
                        <div class="post-button-container">
                            <button type="submit" class="post-button">Publicar</button>
                        </div>
                    </form>
                    @if(session('success'))
                        <div id="success-alert" class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <hr class="divisor-line">
                    <!-- Área donde se muestran las publicaciones -->
                    <div class="post-container">
                        @foreach($posts as $post)
                            <div class="post">
                                <div class="post-header">
                                <img src="data:{{ $post->user->mime_type }};base64,{{ $post->user->image_data }}" alt="Imagen adjunta">
                                    <div class="post-info">
                                        <h3>{{ $post->user->name }}</h3>
                                        <p>{{ $post->created_at }}</p>
                                    </div>
                                </div>
                                <div class="post-content">
                                    <p>{{ $post->text }}</p>
                                    @if($post->image_data)
                                        <img src="data:{{ $post->mime_type }};base64,{{ $post->image_data }}" alt="Imagen adjunta">
                                    @endif
                                </div>
                                <div class="post-actions">
                                    <hr class="buttons-divisor-line">
                                    <button class="like-button">Me gusta</button>
                                    <span class="like-count">{{ $post->likes_count }}</span>
                                    <button class="dislike-button">No me gusta</button>
                                    <span class="dislike-count">{{ $post->dislikes_count }}</span>
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
    
    // Oculta el mensaje de éxito después de un tiempo.
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.display = 'none';
        }, 3000);
    }
</script>
