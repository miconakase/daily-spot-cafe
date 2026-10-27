<?php
/**
 * Returns $url only if it is a relative path inside this site (e.g.
 * "../page/checkout.php"); otherwise $default. Stops ?redirect= from
 * sending users to another website after login.
 */
function safe_redirect($url, $default = '../page/index.php')
{
    $url = (string) $url;
    if ($url === ''
        || strpbrk($url, ":\\\r\n") !== false
        || str_starts_with($url, '/')
        || !preg_match('#^[A-Za-z0-9_.\-/?=&%]+$#', $url)) {
        return $default;
    }
    return $url;
}
