<?php
// Admin login + same-site check; also sets the JSON content type
require_once __DIR__ . '/../auth/api_guard.php';

require_once __DIR__ . '/../../src/controller/UserController.php';

$userController = new UserController();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'read':
        $users = $userController->index();
        echo json_encode(['success' => true, 'data' => $users]);
        break;

    case 'update':
        $id = (int) ($_POST['user_id'] ?? 0);
        $data = [
            'first_name'   => $_POST['first_name']   ?? '',
            'last_name'    => $_POST['last_name']    ?? '',
            'email'        => $_POST['email']        ?? '',
            'phone_number'  => $_POST['phone_number']  ?? '',
            'role_id'      => $_POST['role_id']      ?? 2,
        ];
        echo json_encode($userController->update($id, $data));
        break;

    case 'delete':
        $id = (int) ($_POST['user_id'] ?? 0);
        echo json_encode($userController->destroy($id));
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
