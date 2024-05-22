// Verificar si el elemento 'success-alert' existe antes de intentar acceder a él
const successAlert = document.getElementById('success-alert');
if (successAlert) {
    setTimeout(function() {
        successAlert.style.display = 'none';
    }, 3000);
}