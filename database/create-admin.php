<?php
// Creates an admin account (role_id = 1).
// Usage: php database/create-admin.php <username> <password>
if (PHP_SAPI !== 'cli') {
    exit("Run from the command line.\n");
}
if ($argc < 3) {
    exit("Usage: php database/create-admin.php <username> <password>\n");
}

require_once __DIR__ . '/../src/controller/UserController.php';

$userController = new UserController();
$result = $userController->store([
    'username'   => $argv[1],
    'password'   => $argv[2],
    'role_id'    => 1,
    'first_name' => 'Admin',
]);

echo $result['message'] . "\n";
exit($result['success'] ? 0 : 1);
