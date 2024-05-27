const successAlert = document.getElementById('success-alert');
if (successAlert) {
    setTimeout(function() {
        successAlert.style.display = 'none';
    }, 3000);
}

const failedAlert = document.getElementById('failed-alert');
if (failedAlert) {
    setTimeout(function() {
        failedAlert.style.display = 'none';
    }, 3000);
}