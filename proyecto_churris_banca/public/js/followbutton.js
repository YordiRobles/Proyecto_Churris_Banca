$(document).ready(function(){
    console.log(isFollowing);
    if (isFollowing) {
        $('.buttonfollow').hide();
        $('.buttonunfollow').show();
    } else {
        $('.buttonunfollow').hide();
        $('.buttonfollow').show();
    }
});



