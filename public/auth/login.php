<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $redirect = $_GET['redirect'] ?? '../page/index.php';
    header("Location: $redirect");
    exit;
}

require_once '../../src/controller/UserController.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $userController = new UserController();
        $response = $userController->login($username, $password);

        if ($response['success']) {
            $_SESSION['user_id'] = $response['user']['user_id'];
            $_SESSION['username'] = $response['user']['username'];
            $_SESSION['role_id'] = $response['user']['role_id'];
            $_SESSION['full_name'] = $response['user']['details']['full_name'] ?? $response['user']['username'];

            $redirect = $_POST['redirect'] ?? '../page/index.php';
            header("Location: $redirect");
            exit;
        } else {
            $error = $response['message'];
        }
    }
}

$redirectParam = $_GET['redirect'] ?? '../page/index.php';
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
                        <?php if ($error): ?>
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

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold" style="color: #3D2B1F;">Password</label>
                                <input type="password" class="form-control input-group-custom" id="password" name="password" placeholder="Enter your password" required>
                            </div>

                            <button type="submit" class="btn btn-login w-100">Sign In</button>
                        </form>

                        <div class="text-center mt-4">
                            <span style="color: #5D4037; font-size: 0.9rem;">Don't have an account? </span>
                            <a href="signup.php" class="auth-link">Sign Up</a>
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
