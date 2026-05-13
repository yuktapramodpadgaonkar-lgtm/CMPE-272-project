<?php
/**
 * SSO entry: OurMarketplace redirects here with ?token=... after signing claims.
 * Optional: ?return=/relative/path (same site only).
 */
require_once __DIR__ . '/includes/marketplace_sso.php';

function sso_safe_return_path(string $return): string {
    $return = trim($return);
    if ($return === '') {
        return 'index.php';
    }
    if (stripos($return, 'http') === 0 || strpos($return, '..') !== false || strlen($return) > 200) {
        return 'index.php';
    }
    $return = ltrim($return, '/');
    if (!preg_match('#^[a-zA-Z0-9_./?=&%-]{1,200}$#', $return)) {
        return 'index.php';
    }
    return $return;
}

$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
$return = isset($_GET['return']) ? (string) $_GET['return'] : 'index.php';

$claims = marketplace_sso_verify_token($token);
if ($claims === null) {
    http_response_code(403);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Sign-in failed</title></head><body>';
    echo '<p>Marketplace sign-in link is invalid or expired. <a href="index.php">Home</a></p>';
    echo '</body></html>';
    exit;
}

marketplace_sso_establish_session($claims);

$target = sso_safe_return_path($return);
header('Location: ' . $target, true, 302);
exit;
