<?php
// Admin login + same-site check; also sets the JSON content type
require_once __DIR__ . '/../auth/api_guard.php';

require_once __DIR__ . '/../../src/controller/MessageController.php';

$messageController = new MessageController();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Anything that changes data must be a POST (GETs skip the same-site check)
if ($action !== 'read' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Use POST for this action']);
    exit;
}

switch ($action) {
    case 'read':
        echo json_encode(['success' => true, 'data' => $messageController->index()]);
        break;

    case 'open':
        // POST because opening a message marks it as read
        $id = (int) ($_POST['message_id'] ?? 0);
        echo json_encode($messageController->open($id));
        break;

    case 'mark_all_read':
        echo json_encode($messageController->markAllAsRead());
        break;

    case 'delete':
        $id = (int) ($_POST['message_id'] ?? 0);
        echo json_encode($messageController->destroy($id));
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
