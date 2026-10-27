// Admin Messages page: open (marks read), reply, delete, search and filter.
const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
const deleteMessageModal = new bootstrap.Modal(document.getElementById('deleteMessageModal'));
let currentMessageId = null;
let pendingDeleteId = null;

function postMessageAction(action, id) {
    const formData = new FormData();
    formData.append('action', action);
    if (id) formData.append('message_id', id);
    return fetch('api/MessagesService.php', { method: 'POST', body: formData }).then(r => r.json());
}

function openMessage(id) {
    postMessageAction('open', id)
        .then(data => {
            if (!data.success) {
                showToast(escapeHtml(data.message || 'Could not open message'), 'error');
                return;
            }
            const msg = data.data;
            currentMessageId = msg.message_id;
            document.getElementById('mdSubject').textContent = msg.subject;
            document.getElementById('mdName').textContent = msg.name;
            document.getElementById('mdEmail').textContent = msg.email;
            document.getElementById('mdDate').textContent = new Date(msg.created_at.replace(' ', 'T'))
                .toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' });
            document.getElementById('mdBody').textContent = msg.message;
            document.getElementById('mdReplyBtn').href = 'mailto:' + encodeURIComponent(msg.email)
                + '?subject=' + encodeURIComponent('Re: ' + msg.subject);

            // Opening counts as reading: update the row without a reload
            const row = document.querySelector(`.message-row[data-message-id="${msg.message_id}"]`);
            if (row) row.classList.remove('unread');

            messageModal.show();
        })
        .catch(() => showToast('Something went wrong!', 'error'));
}

function askDelete(id) {
    pendingDeleteId = id;
    messageModal.hide();
    deleteMessageModal.show();
}

document.addEventListener('click', function(e) {
    const del = e.target.closest('[data-delete-message]');
    if (del) {
        e.stopPropagation();
        askDelete(del.dataset.deleteMessage);
        return;
    }
    const open = e.target.closest('[data-open-message]') || e.target.closest('.message-row');
    if (open) {
        openMessage(open.dataset.openMessage || open.dataset.messageId);
    }
});

document.getElementById('mdDeleteBtn').addEventListener('click', () => askDelete(currentMessageId));

document.getElementById('confirmDeleteMessageBtn').addEventListener('click', function() {
    postMessageAction('delete', pendingDeleteId).then(data => {
        deleteMessageModal.hide();
        showToast(escapeHtml(data.message), data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 1200);
    });
});

document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
    postMessageAction('mark_all_read').then(data => {
        showToast(escapeHtml(data.message), data.success ? 'success' : 'error');
        if (data.success) setTimeout(() => location.reload(), 1200);
    });
});

// Search and unread filter
const searchInput = document.getElementById('messageSearch');
const filterSelect = document.getElementById('messageFilter');

function applyFilters() {
    const term = searchInput.value.trim().toLowerCase();
    const unreadOnly = filterSelect.value === 'unread';
    let visible = 0;
    document.querySelectorAll('.message-row').forEach(row => {
        const show = row.dataset.search.includes(term) && (!unreadOnly || row.classList.contains('unread'));
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const hasRows = document.querySelectorAll('.message-row').length > 0;
    document.getElementById('noMatchRow').style.display = hasRows && visible === 0 ? '' : 'none';
}

searchInput.addEventListener('input', applyFilters);
filterSelect.addEventListener('change', applyFilters);

if (OPEN_MESSAGE_ID) openMessage(OPEN_MESSAGE_ID);
