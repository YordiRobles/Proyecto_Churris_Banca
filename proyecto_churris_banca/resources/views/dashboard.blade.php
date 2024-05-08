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
                    <form class="post-form" action="#" method="POST">
                        <div class="post-inputs">
                            <textarea name="post-content" placeholder="¿Qué estás pensando?" class="post-textarea"></textarea>
                            <input type="file" name="post-image" accept="image/*" class="post-image-input">
                        </div>
                        <div class="post-button-container">
                            <button type="submit" class="post-button">Publicar</button>
                        </div>
                    </form>
                    <hr class="divisor-line">
                    <!-- Área donde se muestran las publicaciones -->
                    <div class="post-container">
                        <!-- Publicación de ejemplo 1 -->
                        <div class="post">
                            <div class="post-header">
                                <img src="img/lupa.jpg" alt="Avatar del usuario">
                                <div class="post-info">
                                    <h3>Nombre del Usuario</h3>
                                    <p>Fecha y hora de la publicación</p>
                                </div>
                            </div>
                            <div class="post-content">
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at mattis leo, nec faucibus augue. Sed leo massa, sodales a varius at, imperdiet et libero. Maecenas hendrerit, enim non tempor euismod, ipsum mauris imperdiet nibh, in fermentum est libero eget ipsum. Fusce in euismod ligula. Nulla in auctor justo, sit amet tincidunt enim. Pellentesque quis dui maximus, rutrum arcu eget, faucibus justo. Praesent interdum est sed quam dictum, id tincidunt eros rutrum. Cras et est hendrerit, congue leo nec, scelerisque dolor. Integer mollis aliquet lobortis. Donec eget neque mattis, lacinia nunc sed, laoreet felis. Integer lacus ante, ullamcorper quis tincidunt non, viverra et lacus. Cras purus erat, egestas vel sodales id, consequat sit amet sapien. Cras pellentesque ac libero id viverra. Aliquam malesuada nunc imperdiet consectetur gravida. Praesent placerat justo risus. </p>
                                <img src="img/WelcomeChurrisBanca.png" alt="Imagen adjunta">
                            </div>
                            <div class="post-actions">
                                <button class="like-button">Me gusta</button>
                                <span class="like-count">100</span>
                                <button class="dislike-button">No me gusta</button>
                                <span class="dislike-count">10</span>
                            </div>
                        </div>
                        
                        <!-- Publicación de ejemplo 2 -->
                        <div class="post">
                            <div class="post-header">
                                <img src="img/lupa.jpg" alt="Avatar del usuario">
                                <div class="post-info">
                                    <h3>Nombre del Usuario</h3>
                                    <p>Fecha y hora de la publicación</p>
                                </div>
                            </div>
                            <div class="post-content">
                                <p>Contenido de la publicación...</p>
                            </div>
                            <div class="post-actions">
                                <button class="like-button">Me gusta</button>
                                <span class="like-count">50</span>
                                <button class="dislike-button">No me gusta</button>
                                <span class="dislike-count">5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

