<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../src/controller/CategoryController.php';
$categoryController = new CategoryController();
$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'read':
        $categories = $categoryController->index();
        echo json_encode(['success' => true, 'data' => $categories]);
        break;
    case 'update':
        $id = (int) ($_POST['category_id'] ?? 0);
        $data = [
            'category_name' => $_POST['category_name'] ?? '',
            'description'   => $_POST['description'] ?? '',
            'is_active'     => $_POST['is_active'] ?? 1,
        ];
        echo json_encode($categoryController->update($id, $data));
        break;
    case 'delete':
        $id = (int) ($_POST['category_id'] ?? 0);
        echo json_encode($categoryController->destroy($id));
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
