<?php
/**
 * Shared admin access rules, used by auth_check.php (pages) and
 * api_guard.php (JSON endpoints).
 *
 * The customer site uses the same session, so being logged in is not
 * enough: the user must have role_id 1 (Admin).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const ADMIN_ROLE_ID = 1;

function is_admin_session()
{
    return isset($_SESSION['user_id'], $_SESSION['role_id'])
        && (int) $_SESSION['role_id'] === ADMIN_ROLE_ID;
}

/**
 * Blocks cross-site requests: anything other than GET/HEAD must come from a
 * page on this same host (checked via the Origin or Referer header).
 */
function is_same_origin_request()
{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($method === 'GET' || $method === 'HEAD') {
        return true;
    }

    $source = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
    if ($source === '' || $source === 'null') {
        return false;
    }

    $sourceHost = parse_url($source, PHP_URL_HOST);
    $sourcePort = parse_url($source, PHP_URL_PORT);
    $sourceHostPort = $sourceHost . ($sourcePort ? ':' . $sourcePort : '');

    return $sourceHost !== null && strcasecmp($sourceHostPort, $_SERVER['HTTP_HOST'] ?? '') === 0;
}
