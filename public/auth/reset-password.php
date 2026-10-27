<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../src/controller/PasswordResetController.php';

$controller = new PasswordResetController();
$token = $_POST['token'] ?? $_GET['token'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $result = $controller->resetPassword($token, $_POST['password'] ?? '', $_POST['confirm_password'] ?? '');
        if ($result['success']) {
            header('Location: login.php?notice=reset');
            exit;
        }
        $error = $result['message'];
    }
}

$tokenValid = $token !== '' && $controller->isValidToken($token);

$base = '../';
$pageTitle = 'Choose a New Password';
?>
<!doctype html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../includes/head.php'; ?>
    <style>
        body { min-height: 100vh; display: flex; align-items: center; }
        .brand-logo { color: #3D2B1F; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4" style="max-width: 420px;">
                <div class="text-center mb-4">
                    <a href="../index.php" class="brand-logo fs-2 text-decoration-none">Daily Spot Cafe</a>
                    <p class="text-brown mt-2 mb-0">Choose a new password</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?php if (!$tokenValid): ?>
                            <div class="text-center">
                                <i class="bi bi-link-45deg fs-1 text-brown"></i>
                                <h6 class="text-dark-brown mt-2">This link is invalid or has expired</h6>
                                <p class="small text-brown">Reset links work once and expire after <?= PASSWORD_RESET_TTL_MINUTES ?> minutes.</p>
                                <a href="forgot-password.php" class="btn btn-brand">Request a New Link</a>
                            </div>
                        <?php else: ?>
                            <?php if ($error): ?>
                                <div class="alert alert-danger py-2 small" role="alert">
                                    <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                                <div class="mb-3">
                                    <label for="password" class="form-label-brand mb-1">New password</label>
                                    <input type="password" id="password" name="password" class="form-control form-control-brand" minlength="8" autocomplete="new-password" required>
                                    <div class="form-text">At least 8 characters.</div>
                                </div>
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label-brand mb-1">Confirm new password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control form-control-brand" minlength="8" autocomplete="new-password" required>
                                </div>
                                <button type="submit" class="btn btn-brand w-100">Reset Password</button>
                            </form>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <a href="login.php" class="auth-link small"><i class="bi bi-arrow-left me-1"></i>Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
