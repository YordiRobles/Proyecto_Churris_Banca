document.addEventListener('DOMContentLoaded', function () {
    const followButton = document.getElementById('followButton');
    const followActionInput = document.getElementById('followAction');
    console.log(followActionInput);
    followButton.addEventListener('click', function () {
        if (followButton.classList.contains('unfollow')) {
            followButton.classList.remove('unfollow');
            followButton.textContent = 'Empezar a seguir';
            followActionInput.value = 'follow';
        } else {
            followButton.classList.add('unfollow');
            followButton.textContent = 'Dejar de seguir';
            followActionInput.value = 'unfollow';
        }
    });
});
