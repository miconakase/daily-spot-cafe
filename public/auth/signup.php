<?php
session_start();
require_once __DIR__ . '/../includes/redirect.php';
require_once __DIR__ . '/../includes/csrf.php';

$redirect = safe_redirect($_POST['redirect'] ?? $_GET['redirect'] ?? '');

if (isset($_SESSION['user_id'])) {
    header('Location: ' . $redirect);
    exit;
}

require_once __DIR__ . '/../../src/controller/UserController.php';

$error = '';
$values = ['username' => '', 'first_name' => '', 'last_name' => '', 'email' => '', 'phone_number' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = array_merge($values, array_intersect_key($_POST, $values));

    if (!csrf_verify()) {
        $error = 'Your session expired. Please try again.';
    } else {
        $userController = new UserController();
        $result = $userController->register($_POST);

        if ($result['success']) {
            // Sign the new customer in and continue where they left off
            session_regenerate_id(true);
            $_SESSION['user_id']   = $result['user_id'];
            $_SESSION['username']  = trim($_POST['username']);
            $_SESSION['role_id']   = 2;
            $_SESSION['full_name'] = trim($_POST['first_name'] . ' ' . $_POST['last_name']);
            $_SESSION['email']     = trim($_POST['email']);
            header('Location: ' . $redirect);
            exit;
        }
        $error = $result['message'];
    }
}

$base = '../';
$pageTitle = 'Sign Up';
$v = fn($name) => htmlspecialchars($values[$name]);
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
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6" style="max-width: 560px;">
                <div class="text-center mb-4">
                    <a href="../index.php" class="brand-logo fs-2 text-decoration-none">Daily Spot Cafe</a>
                    <p class="text-brown mt-2 mb-0">Create your account</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2 small" role="alert">
                                <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label-brand mb-1">First name</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control form-control-brand" maxlength="100" value="<?= $v('first_name') ?>" autocomplete="given-name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label-brand mb-1">Last name</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control form-control-brand" maxlength="100" value="<?= $v('last_name') ?>" autocomplete="family-name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label-brand mb-1">Email</label>
                                    <input type="email" id="email" name="email" class="form-control form-control-brand" maxlength="255" value="<?= $v('email') ?>" autocomplete="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label-brand mb-1">Phone <span class="fw-normal text-muted">(optional)</span></label>
                                    <input type="tel" id="phone_number" name="phone_number" class="form-control form-control-brand" maxlength="20" placeholder="0917 123 4567" value="<?= $v('phone_number') ?>" autocomplete="tel">
                                </div>
                                <div class="col-12">
                                    <label for="username" class="form-label-brand mb-1">Username</label>
                                    <input type="text" id="username" name="username" class="form-control form-control-brand" maxlength="30" pattern="[A-Za-z0-9_.]{3,30}" value="<?= $v('username') ?>" autocomplete="username" required>
                                    <div class="form-text">3-30 characters: letters, numbers, dots or underscores.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label-brand mb-1">Password</label>
                                    <input type="password" id="password" name="password" class="form-control form-control-brand" minlength="8" autocomplete="new-password" required>
                                    <div class="form-text">At least 8 characters.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label-brand mb-1">Confirm password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control form-control-brand" minlength="8" autocomplete="new-password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-brand w-100 mt-4">Create Account</button>
                        </form>

                        <div class="text-center mt-4">
                            <span class="text-brown small">Already have an account? </span>
                            <a href="login.php?redirect=<?= urlencode($redirect) ?>" class="auth-link">Log In</a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="../index.php" class="text-decoration-none small" style="color: #A1887F;">
                        <i class="bi bi-arrow-left me-1"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
