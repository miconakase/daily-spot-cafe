<?php
session_start();
header('Content-Type: application/json');

require_once '../controller/ProductController.php';

$productController = new ProductController();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

/**
 * Parse variants JSON string posted from JS FormData.
 * Expected format: JSON array of {variant_type, size_label, price} objects.
 */
function parseVariants(): array
{
    $raw = $_POST['variants'] ?? '';
    if (empty($raw)) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

switch ($action) {
    case 'create':
        $variants = parseVariants();
        $data = [
            'product_name'      => $_POST['product_name']      ?? '',
            'category_id'       => $_POST['category_id']       ?? '',
            'price'             => $_POST['price']             ?? 0,
            'short_description' => $_POST['short_description'] ?? '',
            'long_description'  => $_POST['long_description']  ?? '',
            'is_published'      => $_POST['is_published']      ?? 1,
            'is_available'      => $_POST['is_available']      ?? 1,
            'variants'          => $variants,
        ];
        echo json_encode($productController->store($data));
        break;

    case 'read':
        $products = $productController->index();
        echo json_encode(['success' => true, 'data' => $products]);
        break;

    case 'update':
        $id       = (int) ($_POST['product_id'] ?? 0);
        $variants = parseVariants();
        $data = [
            'product_name'      => $_POST['product_name']      ?? '',
            'category_id'       => $_POST['category_id']       ?? '',
            'price'             => $_POST['price']             ?? 0,
            'short_description' => $_POST['short_description'] ?? '',
            'long_description'  => $_POST['long_description']  ?? '',
            'is_published'      => $_POST['is_published']      ?? 0,
            'variants'          => $variants,
        ];
        echo json_encode($productController->update($id, $data));
        break;

    case 'delete':
        $id = (int) ($_POST['product_id'] ?? 0);
        echo json_encode($productController->destroy($id));
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>