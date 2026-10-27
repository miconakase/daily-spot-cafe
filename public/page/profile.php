<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=' . urlencode('../page/profile.php'));
    exit;
}

require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../src/controller/UserDetailsController.php';
require_once __DIR__ . '/../../src/controller/UserController.php';

$userId = (int) $_SESSION['user_id'];
$detailsCtrl = new UserDetailsController();

$profileResult = null;
$passwordResult = null;
$formValues = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $invalid = ['success' => false, 'message' => 'Your session expired. Please try again.'];

    if ($action === 'profile') {
        $formValues = $_POST;
        $profileResult = csrf_verify() ? $detailsCtrl->updateProfile($userId, $_POST) : $invalid;
        if ($profileResult['success']) {
            $formValues = null;
            $_SESSION['full_name'] = trim(($_POST['first_name'] ?? '') . ' ' . ($_POST['last_name'] ?? ''));
            $_SESSION['email'] = trim($_POST['email'] ?? '');
        }
    } elseif ($action === 'password') {
        if (!csrf_verify()) {
            $passwordResult = $invalid;
        } elseif (strlen($_POST['new_password'] ?? '') < 8) {
            $passwordResult = ['success' => false, 'message' => 'New password must be at least 8 characters.'];
        } elseif (($_POST['new_password'] ?? '') !== ($_POST['confirm_password'] ?? '')) {
            $passwordResult = ['success' => false, 'message' => 'New passwords do not match.'];
        } else {
            $passwordResult = (new UserController())->changePassword($userId, $_POST['current_password'] ?? '', $_POST['new_password']);
        }
    }
}

$profile = (new UserDetailsController())->getProfile($userId) ?? [];
$v = $formValues ?? $profile;
$field = fn($name) => htmlspecialchars($v[$name] ?? '');

$base = '../';
$active = '';
$pageTitle = 'My Profile';
?>
<!doctype html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body>
    <?php $loginRedirect = '../page/profile.php'; include __DIR__ . '/../includes/navbar.php'; ?>

    <section class="py-5">
        <div class="container py-2" style="max-width: 900px;">
            <h1 class="section-title mb-1">My Profile</h1>
            <p class="text-brown mb-4">Signed in as <strong><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong></p>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card card-brand p-4">
                        <h5 class="fw-bold text-dark-brown mb-3">Personal Information</h5>

                        <?php if ($profileResult): ?>
                            <div class="alert <?= $profileResult['success'] ? 'alert-success' : 'alert-danger' ?> py-2 small" role="alert">
                                <?= htmlspecialchars($profileResult['message']) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="profile">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label-brand mb-1">First name*</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control form-control-brand" maxlength="100" value="<?= $field('first_name') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="middle_name" class="form-label-brand mb-1">Middle name</label>
                                    <input type="text" id="middle_name" name="middle_name" class="form-control form-control-brand" maxlength="100" value="<?= $field('middle_name') ?>">
                                </div>
                                <div class="col-md-8">
                                    <label for="last_name" class="form-label-brand mb-1">Last name*</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control form-control-brand" maxlength="100" value="<?= $field('last_name') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="suffix" class="form-label-brand mb-1">Suffix</label>
                                    <input type="text" id="suffix" name="suffix" class="form-control form-control-brand" maxlength="20" placeholder="Jr., III" value="<?= $field('suffix') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label-brand mb-1">Email*</label>
                                    <input type="email" id="email" name="email" class="form-control form-control-brand" maxlength="255" value="<?= $field('email') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label-brand mb-1">Phone</label>
                                    <input type="tel" id="phone_number" name="phone_number" class="form-control form-control-brand" maxlength="20" placeholder="0917 123 4567" value="<?= $field('phone_number') ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-brand mt-4">Save Changes</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card card-brand p-4">
                        <h5 class="fw-bold text-dark-brown mb-3">Change Password</h5>

                        <?php if ($passwordResult): ?>
                            <div class="alert <?= $passwordResult['success'] ? 'alert-success' : 'alert-danger' ?> py-2 small" role="alert">
                                <?= htmlspecialchars($passwordResult['message']) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="password">
                            <div class="mb-3">
                                <label for="current_password" class="form-label-brand mb-1">Current password</label>
                                <input type="password" id="current_password" name="current_password" class="form-control form-control-brand" autocomplete="current-password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label-brand mb-1">New password</label>
                                <input type="password" id="new_password" name="new_password" class="form-control form-control-brand" minlength="8" autocomplete="new-password" required>
                                <div class="form-text">At least 8 characters.</div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label-brand mb-1">Confirm new password</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control form-control-brand" minlength="8" autocomplete="new-password" required>
                            </div>
                            <button type="submit" class="btn btn-brand w-100">Update Password</button>
                        </form>
                    </div>

                    <a href="orders.php" class="card card-brand p-3 mt-4 text-decoration-none d-flex flex-row align-items-center justify-content-between">
                        <span class="text-dark-brown fw-semibold"><i class="bi bi-receipt me-2"></i>View my orders</span>
                        <i class="bi bi-chevron-right text-brown"></i>
                    </a>
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
