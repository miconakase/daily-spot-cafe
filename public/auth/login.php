<?php
session_start();
require_once __DIR__ . '/../includes/redirect.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . safe_redirect($_GET['redirect'] ?? ''));
    exit;
}

require_once '../../src/controller/UserController.php';

$error = '';
$waitSeconds = 0;
$notice = match ($_GET['notice'] ?? '') {
    'reset' => 'Your password has been reset. Please log in with your new password.',
    default => '',
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $userController = new UserController();
        $response = $userController->login($username, $password);

        if ($response['success']) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $response['user']['user_id'];
            $_SESSION['username'] = $response['user']['username'];
            $_SESSION['role_id'] = $response['user']['role_id'];
            $_SESSION['full_name'] = $response['user']['details']['full_name'] ?? $response['user']['username'];
            $_SESSION['email'] = $response['user']['details']['email'] ?? '';

            header('Location: ' . safe_redirect($_POST['redirect'] ?? ''));
            exit;
        } else {
            $error = $response['message'];
            $waitSeconds = $response['wait_seconds'] ?? 0;
        }
    }
}

$redirectParam = safe_redirect($_POST['redirect'] ?? $_GET['redirect'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Daily Spot Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #FDF8F4; min-height: 100vh; display: flex; align-items: center; }
        .login-card { max-width: 420px; width: 100%; }
        .brand-logo { font-family: 'Playfair Display', serif; font-weight: 800; color: #3D2B1F; }
        .btn-login { background-color: #3D2B1F; color: #fff; border: none; padding: 12px; font-weight: 600; border-radius: 0; transition: background 0.2s; }
        .btn-login:hover { background-color: #2c1f14; color: #fff; }
        .input-group-custom { border-radius: 0; border: 1px solid #E8D5C4; padding: 10px 14px; font-size: 0.95rem; }
        .input-group-custom:focus { border-color: #3D2B1F; box-shadow: none; }
        .auth-link { color: #D35400; text-decoration: none; font-weight: 500; }
        .auth-link:hover { color: #3D2B1F; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 login-card">
                <div class="text-center mb-4">
                    <a href="../index.php" class="brand-logo fs-2 text-decoration-none">Daily Spot Cafe</a>
                    <p class="text-muted mt-2 mb-0" style="color: #5D4037;">Sign in to your account</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?php if ($notice): ?>
                            <div class="alert alert-success py-2 small" role="alert">
                                <i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($notice) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($waitSeconds > 0): ?>
                            <div class="alert alert-warning py-2 small" role="alert" id="lockoutAlert" data-seconds="<?= (int) $waitSeconds ?>">
                                <i class="bi bi-hourglass-split me-1"></i>Too many failed attempts. Try again in <strong id="lockoutSeconds"><?= (int) $waitSeconds ?></strong> seconds.
                            </div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger py-2 small" role="alert">
                                <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectParam) ?>">

                            <div class="mb-3">
                                <label for="username" class="form-label small fw-bold" style="color: #3D2B1F;">Username</label>
                                <input type="text" class="form-control input-group-custom" id="username" name="username" placeholder="Enter your username" required>
                            </div>

                            <div class="mb-2">
                                <label for="password" class="form-label small fw-bold" style="color: #3D2B1F;">Password</label>
                                <input type="password" class="form-control input-group-custom" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <div class="text-end mb-4">
                                <a href="forgot-password.php" class="auth-link small">Forgot password?</a>
                            </div>

                            <button type="submit" class="btn btn-login w-100" id="loginBtn">Sign In</button>
                        </form>

                        <div class="text-center mt-4">
                            <span style="color: #5D4037; font-size: 0.9rem;">Don't have an account? </span>
                            <a href="signup.php?redirect=<?= urlencode($redirectParam) ?>" class="auth-link">Sign Up</a>
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
    <script>
        // Flowchart: show wait time -> wait -> back to login once it elapses
        (function() {
            const alertEl = document.getElementById('lockoutAlert');
            if (!alertEl) return;
            const btn = document.getElementById('loginBtn');
            const counter = document.getElementById('lockoutSeconds');
            let left = parseInt(alertEl.dataset.seconds, 10);
            btn.disabled = true;
            const timer = setInterval(() => {
                left--;
                counter.textContent = left;
                if (left <= 0) {
                    clearInterval(timer);
                    btn.disabled = false;
                    alertEl.className = 'alert alert-info py-2 small';
                    alertEl.textContent = 'You can try logging in again now.';
                }
            }, 1000);
        })();
    </script>
</body>
</html>
