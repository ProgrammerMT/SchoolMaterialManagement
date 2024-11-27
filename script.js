document.addEventListener('DOMContentLoaded', function() {
    const errorMessage = document.querySelector('.error');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.display = 'none';
        }, 5000);
    }
});