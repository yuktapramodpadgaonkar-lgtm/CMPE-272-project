<?php
// Simple admin authentication using a text file with userid:password

function auth_start_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function auth_load_admin_users($filePath = null) {
    if ($filePath === null) {
        $filePath = __DIR__ . '/../data/admin_users.txt';
    }

    $users = array();
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
            $parts = explode(':', $line, 2);
            $user = isset($parts[0]) ? $parts[0] : '';
            $pass = isset($parts[1]) ? $parts[1] : '';
            $users[trim($user)] = trim($pass);
        }
    }

    return $users;
}

function auth_check_credentials($username, $password) {
    $users = auth_load_admin_users();
    return isset($users[$username]) && $users[$username] === $password;
}

function auth_require_admin() {
    auth_start_session();
    if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header('Location: admin-login.php');
        exit;
    }
}

