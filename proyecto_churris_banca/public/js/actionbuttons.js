document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const likeButtonElement = this; // Reference to the like button element
            const dislikeButtonElement = document.querySelector(`.dislike-button[data-post-id="${postId}"]`); // Reference to the corresponding dislike button

            fetch(`/like-post`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ post_id: postId })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                // Update like and dislike counts
                document.getElementById(`like-count-${postId}`).textContent = data.likes_count;
                document.getElementById(`dislike-count-${postId}`).textContent = data.dislikes_count;

                // Toggle button styles
                likeButtonElement.classList.toggle('liked');
                dislikeButtonElement.classList.remove('disliked');
            })
            .catch(error => console.error('Error:', error));
        });
    });

    document.querySelectorAll('.dislike-button').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const dislikeButtonElement = this; // Reference to the dislike button element
            const likeButtonElement = document.querySelector(`.like-button[data-post-id="${postId}"]`); // Reference to the corresponding like button

            fetch(`/dislike-post`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ post_id: postId })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                // Update like and dislike counts
                document.getElementById(`like-count-${postId}`).textContent = data.likes_count;
                document.getElementById(`dislike-count-${postId}`).textContent = data.dislikes_count;

                // Toggle button styles
                dislikeButtonElement.classList.toggle('disliked');
                likeButtonElement.classList.remove('liked');
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
