document.addEventListener('DOMContentLoaded', () => {
    const transactionsPerPage = 10;
    const transactionContainer = document.getElementById('transaction-container');
    if (!transactionContainer) {
        console.error('No se encontró el contenedor de transacciones');
        return;
    }

    const transactions = Array.from(transactionContainer.children);
    const totalPages = Math.ceil(transactions.length / transactionsPerPage);
    let currentPage = 1;

    const prevPageButton = document.getElementById('prev-page');
    const nextPageButton = document.getElementById('next-page');

    const showPage = (page) => {
        const start = (page - 1) * transactionsPerPage;
        const end = start + transactionsPerPage;

        transactions.forEach((transaction, index) => {
            if (index >= start && index < end) {
                transaction.style.display = '';
            } else {
                transaction.style.display = 'none';
            }
        });

        prevPageButton.disabled = page === 1;
        nextPageButton.disabled = page === totalPages;
    };

    if (prevPageButton) {
        prevPageButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });
    } else {
        console.error('No se encontró el botón de página anterior');
    }

    if (nextPageButton) {
        nextPageButton.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        });
    } else {
        console.error('No se encontró el botón de página siguiente');
    }

    showPage(currentPage);
});