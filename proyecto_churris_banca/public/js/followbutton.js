$(document).ready(function(){
    console.log(isFollowing);
    if (isFollowing) {
        $('.buttonfollow').hide(); // Si está siguiendo, ocultar el botón de seguir
        $('.buttonunfollow').show();
    } else {
        $('.buttonunfollow').hide(); // Si no está siguiendo, ocultar el botón de dejar de seguir
        $('.buttonfollow').show();
    }

    // Lógica para manejar el evento de hacer clic en los botones (usando AJAX, por ejemplo)
});



