<?php
session_start();
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../../src/controller/PasswordResetController.php';

$result = null;
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');

    if (!csrf_verify()) {
        $result = ['success' => false, 'message' => 'Your session expired. Please try again.'];
    } else {
        // Absolute link to reset-password.php for the email
        if (APP_URL !== '') {
            $resetUrl = rtrim(APP_URL, '/') . '/public/auth/reset-password.php';
        } else {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $resetUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/reset-password.php';
        }
        $result = (new PasswordResetController())->requestReset($identifier, $resetUrl);
    }
}

$base = '../';
$pageTitle = 'Forgot Password';
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
                    <p class="text-brown mt-2 mb-0">Reset your password</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?php if ($result): ?>
                            <div class="alert <?= $result['success'] ? 'alert-success' : 'alert-danger' ?> py-2 small" role="alert">
                                <?= htmlspecialchars($result['message']) ?>
                            </div>
                            <?php if (!empty($result['dev_link'])): ?>
                                <div class="alert alert-warning py-2 small" role="alert">
                                    <strong>Development mode:</strong> no mail server is set up, so here is the link that would be emailed:<br>
                                    <a href="<?= htmlspecialchars($result['dev_link']) ?>" class="text-break">Open reset link</a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!$result || !$result['success']): ?>
                            <p class="small text-brown">Enter your username or the email on your account and we'll send you a link to choose a new password.</p>
                            <form method="POST">
                                <?= csrf_field() ?>
                                <div class="mb-4">
                                    <label for="identifier" class="form-label-brand mb-1">Username or email</label>
                                    <input type="text" id="identifier" name="identifier" class="form-control form-control-brand" value="<?= htmlspecialchars($identifier) ?>" autocomplete="username" required>
                                </div>
                                <button type="submit" class="btn btn-brand w-100">Send Reset Link</button>
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
