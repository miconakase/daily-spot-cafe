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

// --- Order details and Mark as Paid ---

const orderDetailsModalEl = document.getElementById('orderDetailsModal');
const PAYMENT_LABELS = { COD: 'Cash on Pickup', QRPH: 'QR PH' };
let detailsOrderId = null;

function badge(text, cls) {
    return `<span class="badge ${cls}">${escapeHtml(text)}</span>`;
}

function statusBadgeClass(status) {
    return {
        Pending: 'bg-warning-subtle text-warning',
        Processing: 'bg-info-subtle text-info',
        Ready: 'bg-primary-subtle text-primary',
        Completed: 'bg-success-subtle text-success',
        Cancelled: 'bg-danger-subtle text-danger',
        Paid: 'bg-success-subtle text-success',
        Refunded: 'bg-secondary-subtle text-secondary'
    }[status] || 'bg-secondary-subtle text-secondary';
}

function renderOrderDetails(order) {
    const created = new Date(order.created_at.replace(' ', 'T'));
    document.getElementById('orderDetailsTitle').textContent = 'Order #' + order.order_id;
    document.getElementById('orderDetailsDate').textContent = created.toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' });

    const c = order.customer;
    document.getElementById('odCustomer').innerHTML = c
        ? `<div class="fw-semibold">${escapeHtml(c.name)}</div>
           ${c.email ? `<div class="small"><i class="bi bi-envelope me-1"></i><a href="mailto:${escapeHtml(c.email)}">${escapeHtml(c.email)}</a></div>` : ''}
           ${c.phone ? `<div class="small"><i class="bi bi-telephone me-1"></i>${escapeHtml(c.phone)}</div>` : ''}`
        : '<span class="text-muted">No customer on file</span>';

    document.getElementById('odPayment').innerHTML = `
        <div class="fw-semibold">${escapeHtml(PAYMENT_LABELS[order.payment_type] || order.payment_type)}
            ${badge(order.payment_status, statusBadgeClass(order.payment_status))}</div>
        ${order.reference_number ? `<div class="small">Reference: <span class="font-monospace">${escapeHtml(order.reference_number)}</span></div>` : ''}
        ${order.payment_type === 'QRPH' && order.payment_status !== 'Paid' ? '<div class="small text-muted">Check this reference in your QR PH account before marking as paid.</div>' : ''}`;

    document.getElementById('odItems').innerHTML = order.items.length
        ? order.items.map(item => `
            <tr>
                <td class="fw-semibold">${escapeHtml(item.product_name || 'Deleted product')}</td>
                <td class="text-muted small">${escapeHtml([item.variant_type, item.size_label].filter(Boolean).join(', ') || '—')}</td>
                <td class="text-center">${Number(item.quantity)}</td>
                <td class="text-end">${formatPeso(item.unit_price)}</td>
                <td class="text-end">${formatPeso(item.subtotal)}</td>
            </tr>`).join('')
        : '<tr><td colspan="5" class="text-center text-muted">No items</td></tr>';

    document.getElementById('odTotal').textContent = formatPeso(order.total_amount);
    document.getElementById('odNotes').innerHTML = order.notes
        ? `<span class="fw-bold text-muted">NOTES:</span> ${escapeHtml(order.notes)}`
        : '';
    document.getElementById('odStatus').innerHTML = 'Order status: ' + badge(order.order_status, statusBadgeClass(order.order_status));

    const canMarkPaid = order.payment_status !== 'Paid' && order.order_status !== 'Cancelled';
    document.getElementById('odMarkPaidBtn').style.display = canMarkPaid ? '' : 'none';
}

function openOrderDetails(id) {
    detailsOrderId = id;
    document.getElementById('orderDetailsLoading').style.display = '';
    document.getElementById('orderDetailsContent').style.display = 'none';
    document.getElementById('odMarkPaidBtn').style.display = 'none';
    bootstrap.Modal.getOrCreateInstance(orderDetailsModalEl).show();

    fetch('api/OrdersService.php?action=details&order_id=' + encodeURIComponent(id))
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('orderDetailsLoading').textContent = data.message || 'Could not load order.';
                return;
            }
            renderOrderDetails(data.data);
            document.getElementById('orderDetailsLoading').style.display = 'none';
            document.getElementById('orderDetailsContent').style.display = '';
        })
        .catch(() => {
            document.getElementById('orderDetailsLoading').textContent = 'Could not load order.';
        });
}

function markOrderPaid(id) {
    const formData = new FormData();
    formData.append('action', 'mark_paid');
    formData.append('order_id', id);

    return fetch('api/OrdersService.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            showToast(escapeHtml(data.message), data.success ? 'success' : 'error');
            if (data.success) setTimeout(() => location.reload(), 1500);
        })
        .catch(() => showToast('Something went wrong!', 'error'));
}

document.addEventListener('click', function(e) {
    const viewBtn = e.target.closest('[data-view-order]');
    if (viewBtn) {
        openOrderDetails(viewBtn.dataset.viewOrder);
        return;
    }
    const paidBtn = e.target.closest('[data-mark-paid]');
    if (paidBtn) {
        paidBtn.disabled = true;
        markOrderPaid(paidBtn.dataset.markPaid).finally(() => { paidBtn.disabled = false; });
    }
});

document.getElementById('odMarkPaidBtn').addEventListener('click', function() {
    this.disabled = true;
    markOrderPaid(detailsOrderId).finally(() => { this.disabled = false; });
});

// --- Add Order: Hot/Iced and size options, live price, save ---

const addOrderForm = document.getElementById('addOrderForm');
const addProductSelect = document.getElementById('addProductSelect');
const addVariantType = document.getElementById('addVariantType');
const addSizeLabel = document.getElementById('addSizeLabel');
const addQuantity = addOrderForm.querySelector('[name="quantity"]');

function selectedProductVariants() {
    const option = addProductSelect.selectedOptions[0];
    return option && option.dataset.variants ? JSON.parse(option.dataset.variants) : [];
}

function fillSelect(select, values) {
    select.replaceChildren(...values.map(value => {
        const opt = document.createElement('option');
        opt.value = value;
        opt.textContent = value;
        return opt;
    }));
}

// Keep only the option pairs that exist (e.g. a size offered Iced but not Hot)
function matchingVariant() {
    const variants = selectedProductVariants();
    const type = addVariantType.disabled ? null : addVariantType.value || null;
    const size = addSizeLabel.disabled ? null : addSizeLabel.value || null;
    return variants.find(v => (v.variant_type || null) === type && (v.size_label || null) === size);
}

function updateAddPrice() {
    const preview = document.getElementById('addPricePreview');
    const option = addProductSelect.selectedOptions[0];
    if (!option || !option.value) {
        preview.textContent = '';
        return;
    }
    const variants = selectedProductVariants();
    const qty = Math.max(1, parseInt(addQuantity.value, 10) || 1);
    let unit = parseFloat(option.dataset.price);
    if (variants.length) {
        const match = matchingVariant();
        if (!match) {
            preview.textContent = 'That combination is not available for this product.';
            return;
        }
        unit = parseFloat(match.price);
    }
    preview.textContent = `${formatPeso(unit)} each · ${formatPeso(unit * qty)} total`;
}

addProductSelect.addEventListener('change', function() {
    const variants = selectedProductVariants();
    const types = [...new Set(variants.map(v => v.variant_type).filter(Boolean))];
    const sizes = [...new Set(variants.map(v => v.size_label).filter(Boolean))];

    fillSelect(addVariantType, types);
    fillSelect(addSizeLabel, sizes);
    addVariantType.disabled = types.length === 0;
    addSizeLabel.disabled = sizes.length === 0;
    document.getElementById('addVariantCol').style.display = types.length ? '' : 'none';
    document.getElementById('addSizeCol').style.display = sizes.length ? '' : 'none';
    document.getElementById('addOptionsRow').style.display = variants.length ? '' : 'none';
    updateAddPrice();
});

[addVariantType, addSizeLabel, addQuantity].forEach(el => el.addEventListener('input', updateAddPrice));

document.getElementById('addPaymentType').addEventListener('change', function() {
    document.getElementById('addReferenceRow').style.display = this.value === 'QRPH' ? '' : 'none';
});

addOrderForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(addOrderForm); // disabled option selects are left out
    formData.append('action', 'create');
    if (document.getElementById('addPaymentType').value !== 'QRPH') formData.delete('reference_number');

    const submitBtn = addOrderForm.querySelector('[type="submit"]');
    submitBtn.disabled = true;

    fetch('api/OrdersService.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addOrderModal')).hide();
                showToast(escapeHtml(`Order #${data.order_id} created (${formatPeso(data.total_amount)})`), 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(escapeHtml(data.message), 'error');
            }
        })
        .catch(() => showToast('Something went wrong!', 'error'))
        .finally(() => { submitBtn.disabled = false; });
});
