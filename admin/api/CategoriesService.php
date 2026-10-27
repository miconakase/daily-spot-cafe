<?php
// Admin login + same-site check; also sets the JSON content type
require_once __DIR__ . '/../auth/api_guard.php';
require_once __DIR__ . '/../../src/controller/CategoryController.php';
$categoryController = new CategoryController();
$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'read':
        $categories = $categoryController->index();
        echo json_encode(['success' => true, 'data' => $categories]);
        break;
    case 'create':
        echo json_encode($categoryController->store([
            'category_name' => trim($_POST['category_name'] ?? ''),
            'description'   => $_POST['description'] ?? '',
            'is_active'     => $_POST['is_active'] ?? 1,
            'has_variants'  => !empty($_POST['has_variants']) ? 1 : 0,
            'has_sizes'     => !empty($_POST['has_sizes']) ? 1 : 0,
        ]));
        break;
    case 'update':
        $id = (int) ($_POST['category_id'] ?? 0);
        $data = [
            'category_name' => trim($_POST['category_name'] ?? ''),
            'description'   => $_POST['description'] ?? '',
            'is_active'     => $_POST['is_active'] ?? 1,
        ];
        // Only change the option flags when the form sent them
        foreach (['has_variants', 'has_sizes'] as $flag) {
            if (isset($_POST[$flag])) {
                $data[$flag] = !empty($_POST[$flag]) ? 1 : 0;
            }
        }
        echo json_encode($categoryController->update($id, $data));
        break;
    case 'delete':
        $id = (int) ($_POST['category_id'] ?? 0);
        echo json_encode($categoryController->destroy($id));
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
