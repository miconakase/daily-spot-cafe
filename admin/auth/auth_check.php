<?php
require_once __DIR__ . '/admin_session.php';

// Customers share this session, so require the admin role, not just a login
if (!is_admin_session()) {
    header("Location: /daily-spot-cafe/admin/auth/login.php");
    exit();
}

if (!is_same_origin_request()) {
    http_response_code(403);
    exit('Request blocked: cross-site request.');
}

// Ensure all details are in session (for users who were logged in before the update)
if (!isset($_SESSION['email']) || !isset($_SESSION['first_name'])) {
    require_once __DIR__ . '/../../src/controller/UserController.php';
    $userController = new UserController();
    $userDetails = $userController->show($_SESSION['user_id'])['details'];
    
    if ($userDetails) {
        $_SESSION['first_name'] = $userDetails['first_name'] ?? '';
        $_SESSION['last_name'] = $userDetails['last_name'] ?? '';
        $_SESSION['email'] = $userDetails['email'] ?? '';
        $_SESSION['phone_number'] = $userDetails['phone_number'] ?? '';
        $_SESSION['full_name'] = $userDetails['full_name'] ?? $_SESSION['username'];
    }
}
?>
