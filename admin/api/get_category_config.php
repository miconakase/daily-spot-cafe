<?php
// Admin login + same-site check; also sets the JSON content type
require_once __DIR__ . '/../auth/api_guard.php';

require_once __DIR__ . '/../../src/controller/CategoryController.php';

$categoryController = new CategoryController();
$id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid category_id']);
    exit;
}

echo json_encode($categoryController->getCategoryConfig($id));
?>
