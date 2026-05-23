<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../src/controller/CategoryController.php';

$categoryController = new CategoryController();
$id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid category_id']);
    exit;
}

echo json_encode($categoryController->getCategoryConfig($id));
?>
