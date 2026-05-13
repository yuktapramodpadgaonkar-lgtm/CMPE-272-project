<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sc_site_user_is_logged_in(): bool
{
    return isset($_SESSION['site_user']) && is_array($_SESSION['site_user']) && !empty($_SESSION['site_user']['id']);
}

function sc_site_user(): ?array
{
    return sc_site_user_is_logged_in() ? $_SESSION['site_user'] : null;
}

function sc_site_user_login(array $userRow): void
{
    $_SESSION['site_user'] = [
        'id' => (int) ($userRow['id'] ?? 0),
        'marketplace_user_id' => (int) ($userRow['marketplace_user_id'] ?? 0),
        'username' => (string) ($userRow['username'] ?? ''),
        'full_name' => (string) ($userRow['full_name'] ?? ''),
        'email' => (string) ($userRow['email'] ?? ''),
    ];
}

function sc_site_user_logout(): void
{
    unset($_SESSION['site_user']);
    unset($_SESSION['marketplace_token_pending_sync']);
    unset($_SESSION['marketplace_access_token']);
    unset($_SESSION['marketplace_user_id']);
    unset($_SESSION['marketplace_username']);
    $_SESSION['clear_marketplace_token_js'] = true;
}

function sc_require_site_user(string $redirect = 'sso/start.php'): void
{
    if (!sc_site_user_is_logged_in()) {
        header('Location: ' . $redirect);
        exit;
    }
}
