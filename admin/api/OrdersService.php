<?php
// Admin login + same-site check; also sets the JSON content type
require_once __DIR__ . '/../auth/api_guard.php';

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
            'reference_number' => trim($_POST['reference_number'] ?? '') !== '' ? strtoupper(trim($_POST['reference_number'])) : null,
            'order_status'   => $_POST['order_status']   ?? 'Pending',
            'notes'          => $_POST['notes']          ?? '',
            'items'          => [
                [
                    'product_id'   => $_POST['product_id']   ?? null,
                    'variant_type' => $_POST['variant_type'] ?? null,
                    'size_label'   => $_POST['size_label']   ?? null,
                    'quantity'     => $_POST['quantity']     ?? 1,
                ]
            ],
        ];
        echo json_encode($orderController->store($data));
        break;

    case 'update':
        $id   = (int) ($_POST['order_id'] ?? 0);
        // Only send fields that were submitted, so missing ones keep their value
        $data = [];
        foreach (['payment_status', 'payment_type', 'order_status'] as $field) {
            if (($_POST[$field] ?? '') !== '') {
                $data[$field] = $_POST[$field];
            }
        }
        if (isset($_POST['notes'])) {
            $data['notes'] = $_POST['notes'];
        }
        echo json_encode($orderController->update($id, $data));
        break;

    case 'details':
        $id = (int) ($_POST['order_id'] ?? $_GET['order_id'] ?? 0);
        echo json_encode($orderController->getAdminOrderDetails($id));
        break;

    case 'mark_paid':
        $id = (int) ($_POST['order_id'] ?? 0);
        echo json_encode($orderController->markPaid($id));
        break;

    case 'delete':
        $id = (int) ($_POST['order_id'] ?? 0);
        echo json_encode($orderController->destroy($id));
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
