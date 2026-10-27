// Escapes text before inserting it into HTML
function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value == null ? '' : String(value);
    return div.innerHTML.replace(/"/g, '&quot;');
}

function formatPeso(amount) {
    return '₱' + Number(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

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
