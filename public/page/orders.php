<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=' . urlencode('../page/orders.php'));
    exit;
}

require_once __DIR__ . '/../../src/controller/OrderController.php';
$orders = (new OrderController())->getCustomerOrders((int) $_SESSION['user_id']);

$base = '../';
$active = '';
$pageTitle = 'My Orders';

function peso($amount)
{
    return '₱' . number_format((float) $amount, 2);
}
?>
<!doctype html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
    <style>
        .order-item + .order-item { border-top: 1px solid #f0e6dd; }
        .qty-input { width: 80px; }
    </style>
</head>
<body>
    <?php $loginRedirect = '../page/orders.php'; include __DIR__ . '/../includes/navbar.php'; ?>

    <section class="py-5">
        <div class="container py-2" style="max-width: 900px;">
            <h1 class="section-title mb-1">My Orders</h1>
            <p class="text-brown mb-4">Track your orders. Pending orders can still be edited or cancelled.</p>

            <?php if (empty($orders)): ?>
                <div class="card card-brand p-5 text-center">
                    <i class="bi bi-receipt fs-1 text-brown"></i>
                    <h5 class="mt-3 text-dark-brown">No orders yet</h5>
                    <p class="text-brown">Your orders will appear here once you check out.</p>
                    <div><a href="index.php" class="btn btn-brand">Browse the Menu</a></div>
                </div>
            <?php endif; ?>

            <?php foreach ($orders as $order):
                $isPending = $order['order_status'] === 'Pending';
                $orderJson = [
                    'order_id' => (int) $order['order_id'],
                    'notes'    => $order['notes'] ?? '',
                    'items'    => array_map(fn($i) => [
                        'order_item_id' => (int) $i['order_item_id'],
                        'name'          => $i['product_name'],
                        'options'       => implode(', ', array_filter([$i['variant_type'], $i['size_label']])),
                        'quantity'      => (int) $i['quantity'],
                        'unit_price'    => (float) $i['unit_price'],
                    ], $order['items']),
                ];
            ?>
                <div class="card card-brand mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <h5 class="fw-bold text-dark-brown mb-0">Order #<?= (int) $order['order_id'] ?></h5>
                                <div class="small text-muted"><?= htmlspecialchars(date('M j, Y g:i A', strtotime($order['created_at']))) ?></div>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="status-badge status-<?= htmlspecialchars($order['order_status']) ?>"><?= htmlspecialchars($order['order_status']) ?></span>
                            </div>
                        </div>

                        <?php foreach ($order['items'] as $item):
                            $options = implode(', ', array_filter([$item['variant_type'], $item['size_label']])); ?>
                            <div class="order-item d-flex justify-content-between py-2">
                                <div>
                                    <span class="text-dark-brown fw-semibold"><?= htmlspecialchars($item['product_name'] ?? 'Item') ?></span>
                                    <?php if ($options): ?><span class="small text-muted">(<?= htmlspecialchars($options) ?>)</span><?php endif; ?>
                                    <div class="small text-brown"><?= (int) $item['quantity'] ?> × <?= peso($item['unit_price']) ?></div>
                                </div>
                                <div class="text-dark-brown"><?= peso($item['subtotal']) ?></div>
                            </div>
                        <?php endforeach; ?>

                        <div class="d-flex justify-content-between border-top pt-3 mt-2">
                            <div class="small text-brown">
                                <div>
                                    Payment: <?= htmlspecialchars(['COD' => 'Cash on Pickup', 'QRPH' => 'QR PH'][$order['payment_type']] ?? $order['payment_type']) ?>
                                    <span class="status-badge status-<?= htmlspecialchars($order['payment_status']) ?> ms-1"><?= htmlspecialchars($order['payment_status']) ?></span>
                                </div>
                                <?php if (!empty($order['reference_number'])): ?>
                                    <div>Reference: <?= htmlspecialchars($order['reference_number']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($order['notes'])): ?>
                                    <div>Notes: <?= nl2br(htmlspecialchars($order['notes'])) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="fs-5 fw-bold text-dark-brown"><?= peso($order['total_amount']) ?></div>
                        </div>

                        <?php if ($isPending): ?>
                            <div class="d-flex gap-2 justify-content-end mt-3">
                                <button class="btn btn-outline-brand btn-sm edit-order-btn" data-order="<?= htmlspecialchars(json_encode($orderJson)) ?>">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button class="btn btn-outline-danger btn-sm cancel-order-btn" style="border-radius: 0;" data-order-id="<?= (int) $order['order_id'] ?>">
                                    <i class="bi bi-x-circle me-1"></i>Cancel Order
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Edit order modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 0;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-dark-brown" id="editModalLabel">Edit Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="small text-brown">Change quantities or set one to 0 to remove that item.</p>
                    <div id="editItems"></div>
                    <label for="editNotes" class="form-label-brand mt-3 mb-1">Notes</label>
                    <textarea id="editNotes" class="form-control form-control-brand" rows="3" maxlength="500"></textarea>
                    <div class="d-flex justify-content-between mt-3 fw-bold text-dark-brown"><span>New total</span><span id="editTotal"></span></div>
                    <div id="editError" class="alert alert-danger py-2 small mt-3 mb-0" style="display: none;"></div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-brand" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-brand" id="saveEditBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel confirmation modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-2" style="border-radius: 0;">
                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    <h5 class="fw-bold text-dark-brown mt-2" id="cancelModalLabel">Cancel order <span id="cancelOrderLabel"></span>?</h5>
                    <p class="text-brown small mb-0">This can't be undone. If you already paid via QR PH, the cafe will arrange a refund.</p>
                    <div id="cancelError" class="alert alert-danger py-2 small mt-3 mb-0" style="display: none;"></div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-outline-brand" data-bs-dismiss="modal">Keep Order</button>
                    <button type="button" class="btn btn-danger" style="border-radius: 0;" id="confirmCancelBtn">Yes, Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/javascript/cart.js"></script>
    <script src="../assets/javascript/nav.js"></script>
    <script src="../assets/javascript/my-orders.js"></script>
</body>
</html>
