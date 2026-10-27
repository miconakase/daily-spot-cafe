<?php
// Customer order actions. Accepts JSON POST bodies only: browsers will not
// send a cross-site application/json POST without CORS, so this also guards
// against cross-site request forgery.
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../src/controller/OrderController.php';

function respond($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    respond(['success' => false, 'message' => 'Please log in first.', 'login_required' => true], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== 0) {
    respond(['success' => false, 'message' => 'Invalid request'], 400);
}

$input  = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    respond(['success' => false, 'message' => 'Invalid request'], 400);
}

$userId = (int) $_SESSION['user_id'];
$orders = new OrderController();

switch ($input['action'] ?? '') {
    case 'place':
        $items = [];
        foreach (($input['items'] ?? []) as $item) {
            $items[] = [
                'product_id'   => (int) ($item['product_id'] ?? 0),
                'variant_type' => $item['variant_type'] ?? null,
                'size_label'   => $item['size_label'] ?? null,
                'quantity'     => (int) ($item['quantity'] ?? 0),
            ];
        }
        respond($orders->placeCustomerOrder($userId, [
            'items'            => $items,
            'payment_type'     => $input['payment_type'] ?? '',
            'reference_number' => $input['reference_number'] ?? '',
            'notes'            => $input['notes'] ?? '',
        ]));
        break;

    case 'cancel':
        respond($orders->cancelCustomerOrder($userId, (int) ($input['order_id'] ?? 0)));
        break;

    case 'update':
        $quantities = [];
        foreach (($input['quantities'] ?? []) as $itemId => $qty) {
            $quantities[(int) $itemId] = (int) $qty;
        }
        respond($orders->updateCustomerOrder($userId, (int) ($input['order_id'] ?? 0), $quantities, $input['notes'] ?? ''));
        break;

    default:
        respond(['success' => false, 'message' => 'Invalid action'], 400);
}
?>
