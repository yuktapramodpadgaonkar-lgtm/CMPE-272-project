<?php
/**
 * Register / login for site_accounts (OurMarketplace-style fields).
 */
require_once __DIR__ . '/db.php';

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

    $stmt = $mysqli->prepare('SELECT id, username, password_hash, full_name FROM site_accounts WHERE username = ? OR email = ? LIMIT 1');
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

    require_once __DIR__ . '/site_session.php';
    site_user_set_session((int) $row['id'], (string) $row['username'], (string) $row['full_name']);
    return null;
}
