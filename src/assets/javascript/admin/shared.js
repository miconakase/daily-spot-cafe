function showToast(message, type) {
    const existing = document.querySelector('.toast-notification');
    if (existing) existing.remove();

    const icon = type === 'error' ? 'bi-x-circle-fill' : 'bi-check-circle-fill';
    const toast = document.createElement('div');
    toast.className = 'toast-notification' + (type === 'error' ? ' error' : '');
    toast.innerHTML = '<i class="bi ' + icon + ' toast-icon ' + (type === 'error' ? 'error' : 'success') + '"></i>'
                    + '<span class="toast-message">' + message + '</span>';
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 400);
    }, 5000);
}
