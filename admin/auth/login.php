<?php
require_once __DIR__ . '/admin_session.php';

// If already logged in as admin, redirect to dashboard
if (is_admin_session()) {
    header("Location: ../dashboard.php");
    exit();
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
            if ($response['user']['role_id'] != 1) {
                $error = "Access denied. Administrator privileges required.";
            } else {
                // New session ID on login so a planted one can't be reused
                session_regenerate_id(true);
                $_SESSION['user_id'] = $response['user']['user_id'];
                $_SESSION['username'] = $response['user']['username'];
                $_SESSION['role_id'] = $response['user']['role_id'];
                $_SESSION['first_name'] = $response['user']['details']['first_name'] ?? '';
                $_SESSION['last_name'] = $response['user']['details']['last_name'] ?? '';
                $_SESSION['email'] = $response['user']['details']['email'] ?? '';
                $_SESSION['phone_number'] = $response['user']['details']['phone_number'] ?? '';
                $_SESSION['full_name'] = $response['user']['details']['full_name'] ?? $response['user']['username'];
                
                header("Location: ../dashboard.php");
                exit();
            }
        } else {
            $error = $response['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Daily Spot Cafe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../src/assets/css/admin/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand-section">
                <div class="brand-logo">
                    <i class="bi bi-cup-hot-fill"></i>
                </div>
                <h1 class="brand-name">Daily Spot Cafe</h1>
                <p class="brand-tagline">Administration Page</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               class="form-control" 
                               placeholder="Enter your username" 
                               required 
                               autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="••••••••" 
                               required>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    <span>Sign In</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="footer-links">
                <a href="../../public/index.php" class="footer-link">
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Public Site
                </a>
            </div>
        </div>
    </div>
</body>
</html>
