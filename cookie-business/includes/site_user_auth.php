<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sc_site_user_is_logged_in(): bool
{
    if (isset($_SESSION['site_user']) && is_array($_SESSION['site_user']) && !empty($_SESSION['site_user']['id'])) {
        return true;
    }

    return !empty($_SESSION['site_user_id']) && (int) $_SESSION['site_user_id'] > 0;
}

function sc_site_user(): ?array
{
    if (isset($_SESSION['site_user']) && is_array($_SESSION['site_user']) && !empty($_SESSION['site_user']['id'])) {
        return $_SESSION['site_user'];
    }

    if (!empty($_SESSION['site_user_id']) && (int) $_SESSION['site_user_id'] > 0) {
        return [
            'id' => (int) $_SESSION['site_user_id'],
            'marketplace_user_id' => 0,
            'username' => (string) ($_SESSION['site_username'] ?? ''),
            'full_name' => (string) ($_SESSION['site_full_name'] ?? ''),
            'email' => (string) ($_SESSION['site_email'] ?? ''),
        ];
    }

    return null;
}

function sc_site_user_login(array $userRow): void
{
    $user = [
        'id' => (int) ($userRow['id'] ?? 0),
        'marketplace_user_id' => (int) ($userRow['marketplace_user_id'] ?? 0),
        'username' => (string) ($userRow['username'] ?? ''),
        'full_name' => (string) ($userRow['full_name'] ?? ''),
        'email' => (string) ($userRow['email'] ?? ''),
    ];
    $_SESSION['site_user'] = $user;
    $_SESSION['site_user_id'] = $user['id'];
    $_SESSION['site_username'] = $user['username'];
    $_SESSION['site_full_name'] = $user['full_name'];
    $_SESSION['site_email'] = $user['email'];
}

function sc_site_user_logout(): void
{
    unset($_SESSION['site_user']);
    unset($_SESSION['site_user_id']);
    unset($_SESSION['site_username']);
    unset($_SESSION['site_full_name']);
    unset($_SESSION['site_email']);
    unset($_SESSION['marketplace_token_pending_sync']);
    unset($_SESSION['marketplace_access_token']);
    unset($_SESSION['marketplace_user_id']);
    unset($_SESSION['marketplace_username']);
    $_SESSION['clear_marketplace_token_js'] = true;
}

function sc_require_site_user(string $redirect = 'user_login.php'): void
{
    if (!sc_site_user_is_logged_in()) {
        header('Location: ' . $redirect);
        exit;
    }
}
