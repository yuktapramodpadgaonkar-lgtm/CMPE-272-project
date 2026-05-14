<?php
/**
 * Register / login for site_accounts (OurMarketplace-style fields).
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/site_user_auth.php';

function site_accounts_ensure_table(mysqli $mysqli): void
{
    $mysqli->query("
        CREATE TABLE IF NOT EXISTS site_accounts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_site_accounts_username (username),
            UNIQUE KEY uq_site_accounts_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

/**
 * @return string|null Error message, or null on success
 */
function site_register_from_post(array $post): ?string {
    $username  = isset($post['username']) ? trim((string) $post['username']) : '';
    $email     = isset($post['email']) ? trim((string) $post['email']) : '';
    $full_name = isset($post['full_name']) ? trim((string) $post['full_name']) : '';
    $password  = isset($post['password']) ? (string) $post['password'] : '';
    $confirm   = isset($post['confirm_password']) ? (string) $post['confirm_password'] : '';

    if ($username === '' || strlen($username) < 3) {
        return 'Username is required (at least 3 characters).';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'A valid email is required.';
    }
    if ($full_name === '') {
        return 'Full name is required.';
    }
    if (strlen($password) < 6) {
        return 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        return 'Passwords do not match.';
    }

    try {
        $mysqli = db_connect();
    } catch (Exception $e) {
        return 'Database error. Check db_config.php.';
    }

    site_accounts_ensure_table($mysqli);

    $stmt = $mysqli->prepare('SELECT id FROM site_accounts WHERE username = ? OR email = ? LIMIT 1');
    if ($stmt === false) {
        $mysqli->close();
        return 'Database error.';
    }
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $mysqli->close();
        return 'Username or email is already taken.';
    }
    $stmt->close();

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare('INSERT INTO site_accounts (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)');
    if ($stmt === false) {
        $mysqli->close();
        return 'Could not create account (is site_accounts table installed?). Import sql/site_accounts.sql.';
    }
    $stmt->bind_param('ssss', $username, $email, $hash, $full_name);
    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        $mysqli->close();
        return 'Registration failed: ' . $err;
    }
    $stmt->close();
    $mysqli->close();
    return null;
}

/**
 * @return string|null Error message, or null on success (session set)
 */
function site_login_from_post(array $post): ?string {
    $username = isset($post['username']) ? trim((string) $post['username']) : '';
    $password  = isset($post['password']) ? (string) $post['password'] : '';

    if ($username === '' || $password === '') {
        return 'Username and password are required.';
    }

    try {
        $mysqli = db_connect();
    } catch (Exception $e) {
        return 'Database error. Check db_config.php.';
    }

    site_accounts_ensure_table($mysqli);

    $stmt = $mysqli->prepare('SELECT id, username, email, password_hash, full_name FROM site_accounts WHERE username = ? OR email = ? LIMIT 1');
    if ($stmt === false) {
        $mysqli->close();
        return 'Database error.';
    }
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    $mysqli->close();

    if (!$row || !password_verify($password, $row['password_hash'])) {
        return 'Invalid username or password.';
    }

    sc_site_user_login([
        'id' => (int) $row['id'],
        'marketplace_user_id' => 0,
        'username' => (string) $row['username'],
        'full_name' => (string) $row['full_name'],
        'email' => (string) ($row['email'] ?? $username),
    ]);
    return null;
}
