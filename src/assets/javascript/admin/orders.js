document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-light.btn-sm[data-order-id]');
    if (!btn) return;

    document.getElementById('editOrderId').value = btn.getAttribute('data-order-id');
    document.getElementById('editOrderDisplayId').value = '#' + btn.getAttribute('data-order-id');
    document.getElementById('editOrderCustomer').value = btn.getAttribute('data-customer');
    document.getElementById('editPaymentType').value = btn.getAttribute('data-payment-type');
    document.getElementById('editPaymentStatus').value = btn.getAttribute('data-payment-status');
    document.getElementById('editOrderStatus').value = btn.getAttribute('data-order-status');
    document.getElementById('editOrderNotes').value = btn.getAttribute('data-notes');

    new bootstrap.Modal(document.getElementById('editOrderModal')).show();
});

document.getElementById('editOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('order_id', document.getElementById('editOrderId').value);
    formData.append('payment_type', document.getElementById('editPaymentType').value);
    formData.append('payment_status', document.getElementById('editPaymentStatus').value);
    formData.append('order_status', document.getElementById('editOrderStatus').value);
    formData.append('notes', document.getElementById('editOrderNotes').value);

    fetch('api/OrdersService.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editOrderModal')).hide();
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

function deleteOrder(id) {
    document.getElementById('deleteOrderId').value = id;
    new bootstrap.Modal(document.getElementById('deleteOrderModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const id = document.getElementById('deleteOrderId').value;
    bootstrap.Modal.getInstance(document.getElementById('deleteOrderModal')).hide();

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('order_id', id);

    fetch('api/OrdersService.php', {
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
