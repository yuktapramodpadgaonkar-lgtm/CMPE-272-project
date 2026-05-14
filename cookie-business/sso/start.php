<?php
require_once __DIR__ . '/../includes/site_user_auth.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/sso_client.php';

if (sc_site_user_is_logged_in()) {
    header('Location: ../user_dashboard.php');
    exit;
}

auth_start_session();
if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: ../account.php?sso_error=' . rawurlencode('Log out from admin before signing in as a customer.'));
    exit;
}

$url = sc_sso_login_url();
if ($url === '#' || $url === '') {
    header('Location: ../account.php?sso_error=' . rawurlencode('SSO is not configured. Check config/sso.php.'));
    exit;
}

header('Location: ' . $url, true, 302);
exit;
