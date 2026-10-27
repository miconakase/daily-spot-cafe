// My Orders page: edit and cancel pending orders.
document.addEventListener('DOMContentLoaded', function() {
    const peso = n => '₱' + Number(n).toFixed(2);
    const esc = v => CartManager.escape(v);

    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
    const editItems = document.getElementById('editItems');
    const editNotes = document.getElementById('editNotes');
    const editTotal = document.getElementById('editTotal');
    const editError = document.getElementById('editError');
    const cancelError = document.getElementById('cancelError');
    let current = null;
    let cancelId = null;

    function showError(box, message) {
        box.textContent = message;
        box.style.display = message ? 'block' : 'none';
    }

    async function post(body) {
        const res = await fetch('../api/orders.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (data.login_required) {
            window.location.href = '../auth/login.php?redirect=' + encodeURIComponent('../page/orders.php');
        }
        return data;
    }

    function updateTotal() {
        let total = 0;
        editItems.querySelectorAll('.qty-input').forEach(input => {
            total += Math.max(0, parseInt(input.value, 10) || 0) * parseFloat(input.dataset.price);
        });
        editTotal.textContent = peso(total);
    }

    document.querySelectorAll('.edit-order-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            current = JSON.parse(btn.dataset.order);
            document.getElementById('editModalLabel').textContent = 'Edit Order #' + current.order_id;
            editItems.innerHTML = current.items.map(item => `
                <div class="d-flex align-items-center justify-content-between gap-3 py-2 border-bottom">
                    <div>
                        <div class="fw-semibold text-dark-brown">${esc(item.name)}</div>
                        <div class="small text-muted">${esc(item.options)}${item.options ? ' · ' : ''}${peso(item.unit_price)} each</div>
                    </div>
                    <input type="number" min="0" max="99" class="form-control form-control-brand qty-input"
                           data-item-id="${item.order_item_id}" data-price="${item.unit_price}" value="${item.quantity}"
                           aria-label="Quantity for ${esc(item.name)}">
                </div>`).join('');
            editNotes.value = current.notes;
            showError(editError, '');
            updateTotal();
            editModal.show();
        });
    });

    editItems.addEventListener('input', updateTotal);

    document.getElementById('saveEditBtn').addEventListener('click', async function() {
        const quantities = {};
        editItems.querySelectorAll('.qty-input').forEach(input => {
            quantities[input.dataset.itemId] = parseInt(input.value, 10) || 0;
        });

        this.disabled = true;
        try {
            const data = await post({ action: 'update', order_id: current.order_id, quantities, notes: editNotes.value });
            if (data.success) {
                window.location.reload();
            } else {
                showError(editError, data.message || 'Could not update the order.');
            }
        } catch (e) {
            showError(editError, 'Network error. Please try again.');
        } finally {
            this.disabled = false;
        }
    });

    document.querySelectorAll('.cancel-order-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            cancelId = parseInt(btn.dataset.orderId, 10);
            document.getElementById('cancelOrderLabel').textContent = '#' + cancelId;
            showError(cancelError, '');
            cancelModal.show();
        });
    });

    document.getElementById('confirmCancelBtn').addEventListener('click', async function() {
        this.disabled = true;
        try {
            const data = await post({ action: 'cancel', order_id: cancelId });
            if (data.success) {
                window.location.reload();
            } else {
                showError(cancelError, data.message || 'Could not cancel the order.');
            }
        } catch (e) {
            showError(cancelError, 'Network error. Please try again.');
        } finally {
            this.disabled = false;
        }
    });
});
