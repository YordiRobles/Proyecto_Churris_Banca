$(document).ready(function(){
    if (isFollowing) {
        $('.buttonfollow').hide();
        $('.buttonunfollow').show();
    } else {
        $('.buttonunfollow').hide();
        $('.buttonfollow').show();
    }
});



