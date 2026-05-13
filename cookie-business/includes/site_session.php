<?php
/**
 * Session keys for Sweet Crumb customer login (separate from admin and marketplace SSO).
 */
function site_session_start(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function site_user_is_logged_in(): bool {
    site_session_start();
    return !empty($_SESSION['site_user_id']) && (int) $_SESSION['site_user_id'] > 0;
}

function site_user_id(): ?int {
    site_session_start();
    if (empty($_SESSION['site_user_id'])) {
        return null;
    }
    $id = (int) $_SESSION['site_user_id'];
    return $id > 0 ? $id : null;
}

function site_username(): string {
    site_session_start();
    return isset($_SESSION['site_username']) ? (string) $_SESSION['site_username'] : '';
}

function site_user_full_name(): string {
    site_session_start();
    return isset($_SESSION['site_full_name']) ? (string) $_SESSION['site_full_name'] : '';
}

function site_user_set_session(int $id, string $username, string $fullName): void {
    site_session_start();
    $_SESSION['site_user_id']    = $id;
    $_SESSION['site_username']  = $username;
    $_SESSION['site_full_name']  = $fullName;
}

function site_user_clear_session(): void {
    site_session_start();
    unset($_SESSION['site_user_id'], $_SESSION['site_username'], $_SESSION['site_full_name']);
}
