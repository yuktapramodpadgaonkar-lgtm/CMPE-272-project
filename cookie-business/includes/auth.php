<?php
// Simple admin authentication using a text file with userid:password

function auth_start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function auth_load_admin_users(string $filePath = null): array {
    if ($filePath === null) {
        $filePath = __DIR__ . '/../data/admin_users.txt';
    }

    $users = [];
    if (!file_exists($filePath)) {
        return $users;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $users;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, ':') !== false) {
            [$user, $pass] = explode(':', $line, 2);
            $users[trim($user)] = trim($pass);
        }
    }

    return $users;
}

function auth_check_credentials(string $username, string $password): bool {
    $users = auth_load_admin_users();
    return isset($users[$username]) && $users[$username] === $password;
}

function auth_require_admin(): void {
    auth_start_session();
    if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header('Location: admin-login.php');
        exit;
    }
}

