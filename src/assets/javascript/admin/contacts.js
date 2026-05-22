document.addEventListener('DOMContentLoaded', function() {
    const userDetailsModal = document.getElementById('userDetailsModal');
    if (userDetailsModal) {
        userDetailsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            document.getElementById('modalFullName').textContent = button.getAttribute('data-full-name');
            document.getElementById('modalEmail').textContent = button.getAttribute('data-email');
            document.getElementById('modalPhone').textContent = button.getAttribute('data-phone');
            document.getElementById('modalRole').textContent = button.getAttribute('data-role');
            document.getElementById('modalUserId').textContent = button.getAttribute('data-user-id');
        });
    }
});

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-light.btn-sm[data-user-id]');
    if (!btn) return;

    document.getElementById('editUserId').value = btn.getAttribute('data-user-id');
    document.getElementById('editUserDisplayId').value = btn.getAttribute('data-user-id');
    document.getElementById('editFirstName').value = btn.getAttribute('data-first-name');
    document.getElementById('editLastName').value = btn.getAttribute('data-last-name');
    document.getElementById('editEmail').value = btn.getAttribute('data-email');
    document.getElementById('editPhone').value = btn.getAttribute('data-phone');
    document.getElementById('editRoleId').value = btn.getAttribute('data-role-id');

    new bootstrap.Modal(document.getElementById('editContactModal')).show();
});

document.getElementById('editContactForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('user_id', document.getElementById('editUserId').value);
    formData.append('first_name', document.getElementById('editFirstName').value);
    formData.append('last_name', document.getElementById('editLastName').value);
    formData.append('email', document.getElementById('editEmail').value);
    formData.append('phone_number', document.getElementById('editPhone').value);
    formData.append('role_id', document.getElementById('editRoleId').value);

    fetch('api/UsersService.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editContactModal')).hide();
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 3000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Something went wrong!', 'error');
    });
});

function deleteContact(id) {
    document.getElementById('deleteContactId').value = id;
    new bootstrap.Modal(document.getElementById('deleteContactModal')).show();
}

document.getElementById('confirmDeleteContactBtn').addEventListener('click', function() {
    const id = document.getElementById('deleteContactId').value;
    bootstrap.Modal.getInstance(document.getElementById('deleteContactModal')).hide();

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('user_id', id);

    fetch('api/UsersService.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 3000);
        } else {
            showToast(data.message, 'error');
        }
    });
});
