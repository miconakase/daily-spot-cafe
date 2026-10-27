<?php
session_start();
require_once __DIR__ . '/../../src/config/app.php';

// Flowchart: "Is logged in?" -> NO -> Login Page
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=' . urlencode('../page/checkout.php'));
    exit;
}

require_once __DIR__ . '/../../src/controller/UserDetailsController.php';
$profile = (new UserDetailsController())->getProfile($_SESSION['user_id']);

$qrImageExists = file_exists(__DIR__ . '/../' . QRPH_IMAGE);

$base = '../';
$active = '';
$pageTitle = 'Checkout';
?>
<!doctype html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
    <style>
        .summary-img { width: 56px; height: 56px; object-fit: cover; }
        .summary-row + .summary-row { border-top: 1px solid #f0e6dd; }
    </style>
</head>
<body>
    <?php $loginRedirect = '../page/checkout.php'; include __DIR__ . '/../includes/navbar.php'; ?>

    <section class="py-5">
        <div class="container py-2" style="max-width: 1040px;">
            <a href="index.php" class="text-decoration-none mb-3 d-inline-block text-brown">
                <i class="bi bi-arrow-left me-1"></i>Continue shopping
            </a>
            <h1 class="section-title mb-4">Checkout</h1>

            <!-- Shown when the cart is empty (flowchart: "Does cart have product?" -> NO) -->
            <div id="emptyCart" class="card card-brand p-5 text-center" style="display: none;">
                <i class="bi bi-cart-x fs-1 text-brown"></i>
                <h5 class="mt-3 text-dark-brown">Your cart is empty</h5>
                <p class="text-brown">Add something from the menu before checking out.</p>
                <div><a href="index.php" class="btn btn-brand">Browse the Menu</a></div>
            </div>

            <div id="checkoutContent" class="row g-4" style="display: none;">
                <div class="col-lg-7">
                    <div class="card card-brand p-4 mb-4">
                        <h5 class="fw-bold text-dark-brown mb-3">Order Summary</h5>
                        <div id="summaryItems"></div>
                    </div>

                    <div class="card card-brand p-4">
                        <h5 class="fw-bold text-dark-brown mb-3">Customer</h5>
                        <div class="text-brown">
                            <div><i class="bi bi-person me-2"></i><?= htmlspecialchars($profile['full_name'] ?? $_SESSION['username']) ?></div>
                            <?php if (!empty($profile['phone_number'])): ?>
                                <div><i class="bi bi-telephone me-2"></i><?= htmlspecialchars($profile['phone_number']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($profile['email'])): ?>
                                <div><i class="bi bi-envelope me-2"></i><?= htmlspecialchars($profile['email']) ?></div>
                            <?php endif; ?>
                        </div>
                        <a href="profile.php" class="auth-link small mt-2 d-inline-block">Edit profile</a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card card-brand p-4">
                        <h5 class="fw-bold text-dark-brown mb-3">Payment</h5>

                        <label class="pay-option selected mb-2">
                            <input type="radio" name="payment_type" value="COD" checked>
                            <div>
                                <div class="fw-semibold text-dark-brown"><i class="bi bi-cash-coin me-1"></i>Cash on Pickup</div>
                                <div class="small text-brown">Pay at the counter when you pick up.</div>
                            </div>
                        </label>
                        <label class="pay-option mb-4">
                            <input type="radio" name="payment_type" value="QRPH">
                            <div>
                                <div class="fw-semibold text-dark-brown"><i class="bi bi-qr-code me-1"></i>QR PH</div>
                                <div class="small text-brown">Scan with GCash, Maya or your bank app.</div>
                            </div>
                        </label>

                        <label for="orderNotes" class="form-label-brand mb-1">Notes <span class="fw-normal text-muted">(optional)</span></label>
                        <textarea id="orderNotes" class="form-control form-control-brand mb-4" rows="3" maxlength="500" placeholder="e.g. less ice, pickup at 3 PM"></textarea>

                        <div class="d-flex justify-content-between mb-1 text-brown"><span>Items</span><span id="itemCount">0</span></div>
                        <div class="d-flex justify-content-between mb-4 fs-5 fw-bold text-dark-brown"><span>Total</span><span id="orderTotal">₱0.00</span></div>

                        <div id="checkoutError" class="alert alert-danger py-2 small" style="display: none;"></div>

                        <button id="placeOrderBtn" class="btn btn-brand w-100">
                            <i class="bi bi-bag-check me-2"></i>Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- QR PH payment modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 0;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-dark-brown" id="qrModalLabel">Pay with QR PH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <p class="text-brown mb-3">Scan the code and pay <strong class="text-dark-brown" id="qrAmount">₱0.00</strong> to <?= htmlspecialchars(QRPH_ACCOUNT_NAME) ?>.</p>
                    <div class="qr-box mb-3">
                        <?php if ($qrImageExists): ?>
                            <img src="../<?= htmlspecialchars(QRPH_IMAGE) ?>" alt="QR PH code for <?= htmlspecialchars(QRPH_ACCOUNT_NAME) ?>">
                        <?php else: ?>
                            <i class="bi bi-qr-code fs-1"></i>
                            <span class="small mt-2 px-3">QR code placeholder.<br>Add <code><?= htmlspecialchars(QRPH_IMAGE) ?></code></span>
                        <?php endif; ?>
                    </div>
                    <div class="text-start">
                        <label for="referenceNumber" class="form-label-brand mb-1">Reference number</label>
                        <input type="text" id="referenceNumber" class="form-control form-control-brand" maxlength="30" placeholder="From your payment receipt" autocomplete="off">
                        <div class="form-text">Found on the receipt in your e-wallet or bank app after paying.</div>
                    </div>
                    <div id="qrError" class="alert alert-danger py-2 small mt-3 mb-0 text-start" style="display: none;"></div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-brand" data-bs-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-brand" id="confirmPaymentBtn">Confirm Payment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order success modal -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4" style="border-radius: 0;">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem;"></i>
                <h4 class="section-title mt-2" id="successModalLabel">Order Placed!</h4>
                <p class="text-brown mb-1">Order <strong class="text-dark-brown" id="successOrderId"></strong> &middot; <span id="successTotal"></span></p>
                <p class="text-brown small" id="successNote"></p>
                <div class="d-flex gap-2 justify-content-center mt-2">
                    <a href="orders.php" class="btn btn-brand">View My Orders</a>
                    <a href="index.php" class="btn btn-outline-brand">Back to Menu</a>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/javascript/cart.js"></script>
    <script src="../assets/javascript/nav.js"></script>
    <script src="../assets/javascript/checkout.js"></script>
</body>
</html>
