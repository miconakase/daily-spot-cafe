// Checkout page: order summary, payment choice, QR PH reference, place order.
document.addEventListener('DOMContentLoaded', function() {
    const peso = n => '₱' + Number(n).toFixed(2);
    const esc = v => CartManager.escape(v);

    const emptyCart = document.getElementById('emptyCart');
    const content = document.getElementById('checkoutContent');
    const placeBtn = document.getElementById('placeOrderBtn');
    const errorBox = document.getElementById('checkoutError');
    const qrError = document.getElementById('qrError');
    const refInput = document.getElementById('referenceNumber');
    const confirmBtn = document.getElementById('confirmPaymentBtn');
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));

    function render() {
        const items = CartManager.getItems();
        if (items.length === 0) {
            emptyCart.style.display = 'block';
            content.style.display = 'none';
            return;
        }
        emptyCart.style.display = 'none';
        content.style.display = 'flex';

        document.getElementById('summaryItems').innerHTML = items.map(item => {
            const options = [item.variant_type, item.size_label].filter(Boolean).join(', ');
            const img = item.img_url || 'https://placehold.co/60x60/E8D5C4/3D2B1F?text=N';
            return `
                <div class="summary-row d-flex align-items-center gap-3 py-3">
                    <img src="${esc(img)}" class="summary-img" alt="">
                    <div class="flex-grow-1">
                        <div class="fw-semibold text-dark-brown">${esc(item.product_name)}</div>
                        ${options ? `<div class="small text-muted">${esc(options)}</div>` : ''}
                        <div class="small text-brown">${item.quantity} × ${peso(item.price)}</div>
                    </div>
                    <div class="fw-semibold text-dark-brown">${peso(item.price * item.quantity)}</div>
                </div>`;
        }).join('');

        document.getElementById('itemCount').textContent = CartManager.getCount();
        document.getElementById('orderTotal').textContent = peso(CartManager.getTotal());
        document.getElementById('qrAmount').textContent = peso(CartManager.getTotal());
    }

    function paymentType() {
        return document.querySelector('input[name="payment_type"]:checked').value;
    }

    document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.pay-option').forEach(el =>
                el.classList.toggle('selected', el.contains(document.querySelector('input[name="payment_type"]:checked'))));
        });
    });

    function showError(box, message) {
        box.textContent = message;
        box.style.display = message ? 'block' : 'none';
    }

    async function submitOrder(referenceNumber, button, box) {
        const items = CartManager.getItems().map(i => ({
            product_id: i.product_id,
            variant_type: i.variant_type,
            size_label: i.size_label,
            quantity: i.quantity
        }));

        button.disabled = true;
        showError(box, '');
        try {
            const res = await fetch('../api/orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'place',
                    items,
                    payment_type: paymentType(),
                    reference_number: referenceNumber,
                    notes: document.getElementById('orderNotes').value
                })
            });
            const data = await res.json();

            if (data.login_required) {
                window.location.href = '../auth/login.php?redirect=' + encodeURIComponent('../page/checkout.php');
                return;
            }
            if (!data.success) {
                showError(box, data.message || 'Could not place your order.');
                return;
            }

            CartManager.clear();
            qrModal.hide();
            document.getElementById('successOrderId').textContent = '#' + data.order_id;
            document.getElementById('successTotal').textContent = peso(data.total_amount);
            document.getElementById('successNote').textContent = paymentType() === 'QRPH'
                ? 'We will confirm your QR PH payment shortly. You can track the status in My Orders.'
                : 'Please pay at the counter when you pick up your order.';
            successModal.show();
        } catch (e) {
            showError(box, 'Network error. Please check your connection and try again.');
        } finally {
            button.disabled = false;
        }
    }

    // Flowchart: Place Order -> "Is payment QR PH?" -> YES: QR modal, NO: success
    placeBtn.addEventListener('click', () => {
        showError(errorBox, '');
        if (paymentType() === 'QRPH') {
            showError(qrError, '');
            refInput.value = '';
            qrModal.show();
        } else {
            submitOrder('', placeBtn, errorBox);
        }
    });

    confirmBtn.addEventListener('click', () => {
        const ref = refInput.value.trim();
        if (!/^[A-Za-z0-9-]{6,30}$/.test(ref)) {
            showError(qrError, 'Enter the 6-30 character reference number from your payment receipt.');
            return;
        }
        submitOrder(ref, confirmBtn, qrError);
    });

    render();
});
