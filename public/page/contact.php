<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../src/controller/MessageController.php';

$result = null;
$values = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

// Prefill for logged-in customers
if (isset($_SESSION['user_id'])) {
    $values['name']  = $_SESSION['full_name'] ?? '';
    $values['email'] = $_SESSION['email'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = array_merge($values, array_intersect_key($_POST, $values));

    if (!csrf_verify()) {
        $result = ['success' => false, 'message' => 'Your session expired. Please try again.'];
    } else {
        $result = (new MessageController())->store($_POST, $_SESSION['user_id'] ?? null);
    }

    if ($result['success']) {
        $values['subject'] = '';
        $values['message'] = '';
    }
}

$base = '../';
$active = 'contact';
$pageTitle = 'Contact Us';
?>
<!doctype html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body>
    <?php $loginRedirect = '../page/contact.php'; include __DIR__ . '/../includes/navbar.php'; ?>

    <section class="py-5">
        <div class="container py-2" style="max-width: 1040px;">
            <h1 class="section-title text-center mb-2">Contact Us</h1>
            <p class="text-center text-brown mb-5">Questions, feedback or a big order? Send us a message.</p>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card card-brand p-4">
                        <?php if ($result): ?>
                            <div class="alert <?= $result['success'] ? 'alert-success' : 'alert-danger' ?> py-2 small" role="alert">
                                <i class="bi <?= $result['success'] ? 'bi-check-circle' : 'bi-exclamation-circle' ?> me-1"></i><?= htmlspecialchars($result['message']) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <?= csrf_field() ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label-brand mb-1">Name</label>
                                    <input type="text" id="name" name="name" class="form-control form-control-brand" maxlength="150" value="<?= htmlspecialchars($values['name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label-brand mb-1">Email</label>
                                    <input type="email" id="email" name="email" class="form-control form-control-brand" maxlength="255" value="<?= htmlspecialchars($values['email']) ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="subject" class="form-label-brand mb-1">Subject</label>
                                    <input type="text" id="subject" name="subject" class="form-control form-control-brand" maxlength="200" value="<?= htmlspecialchars($values['subject']) ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label-brand mb-1">Message</label>
                                    <textarea id="message" name="message" class="form-control form-control-brand" rows="6" minlength="10" maxlength="2000" required><?= htmlspecialchars($values['message']) ?></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-brand mt-4"><i class="bi bi-send me-2"></i>Send Message</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card card-brand p-4 h-100">
                        <h5 class="fw-bold text-dark-brown mb-3">Visit or call us</h5>
                        <div class="d-flex flex-column gap-3 text-brown">
                            <div><i class="bi bi-geo-alt me-2 text-accent"></i>123 Cafe Street, Manila</div>
                            <div><i class="bi bi-telephone me-2 text-accent"></i>(02) 1234 5678</div>
                            <div><i class="bi bi-envelope me-2 text-accent"></i>hello@dailyspotcafe.com</div>
                        </div>
                        <h6 class="fw-bold text-dark-brown mt-4 mb-2">Opening Hours</h6>
                        <div class="d-flex flex-column gap-1 text-brown small">
                            <span>Mon - Fri: 7:00 AM - 9:00 PM</span>
                            <span>Saturday: 8:00 AM - 10:00 PM</span>
                            <span>Sunday: 8:00 AM - 8:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/javascript/cart.js"></script>
    <script src="../assets/javascript/nav.js"></script>
</body>
</html>
