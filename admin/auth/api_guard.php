<?php
/**
 * Include at the top of every admin/api/*.php endpoint. Stops the request
 * with a JSON error unless an admin is logged in and the request comes
 * from this site.
 */
require_once __DIR__ . '/admin_session.php';

header('Content-Type: application/json');

if (!is_admin_session()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Admin login required']);
    exit;
}

if (!is_same_origin_request()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Request blocked: cross-site request']);
    exit;
}
