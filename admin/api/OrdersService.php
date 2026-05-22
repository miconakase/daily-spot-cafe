<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../src/controller/OrderController.php';

$orderController = new OrderController();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'read':
        $orders = $orderController->index();
        echo json_encode(['success' => true, 'data' => $orders]);
        break;

    case 'create':
        $data = [
            'user_id'        => $_POST['user_id']        ?? null,
            'payment_status' => $_POST['payment_status'] ?? 'Pending',
            'payment_type'   => $_POST['payment_type']   ?? 'COD',
            'order_status'   => $_POST['order_status']   ?? 'Pending',
            'notes'          => $_POST['notes']          ?? '',
            'items'          => [
                [
                    'product_id' => $_POST['product_id'] ?? null,
                    'quantity'   => $_POST['quantity']   ?? 1,
                ]
            ],
        ];
        echo json_encode($orderController->store($data));
        break;

    case 'update':
        $id   = (int) ($_POST['order_id'] ?? 0);
        $data = [
            'payment_status' => $_POST['payment_status'] ?? '',
            'payment_type'   => $_POST['payment_type']   ?? '',
            'order_status'   => $_POST['order_status']   ?? '',
            'notes'          => $_POST['notes']          ?? '',
        ];
        echo json_encode($orderController->update($id, $data));
        break;

    case 'delete':
        $id = (int) ($_POST['order_id'] ?? 0);
        echo json_encode($orderController->destroy($id));
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
