document.addEventListener('DOMContentLoaded', () => {
    const postsPerPage = 6;
    const postContainer = document.getElementById('post-container');
    const posts = Array.from(postContainer.children);
    const totalPages = Math.ceil(posts.length / postsPerPage);

    let currentPage = 1;

    const prevPageButton = document.getElementById('prev-page');
    const nextPageButton = document.getElementById('next-page');

    const showPage = (page) => {
        const start = (page - 1) * postsPerPage;
        const end = start + postsPerPage;
        
        posts.forEach((post, index) => {
            if (index >= start && index < end) {
                post.style.display = '';
            } else {
                post.style.display = 'none';
            }
        });

        prevPageButton.disabled = page === 1;
        nextPageButton.disabled = page === totalPages;
    };

    prevPageButton.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    });

    nextPageButton.addEventListener('click', () => {
        if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
        }
    });

    showPage(currentPage);
});
