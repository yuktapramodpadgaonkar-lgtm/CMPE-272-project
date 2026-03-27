<?php
/**
 * Public JSON API: list of users from THIS company's database.
 * Other companies fetch this with cURL from combined_users.php
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/db.php';

try {
    $mysqli = db_connect();
    $result = $mysqli->query('SELECT id, name, email, created_at FROM users ORDER BY id ASC');
    if ($result === false) {
        throw new Exception($mysqli->error);
    }
    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = array(
            'id'    => (int) $row['id'],
            'name'  => $row['name'],
            'email' => $row['email'],
            'created_at' => isset($row['created_at']) ? $row['created_at'] : null,
        );
    }
    $mysqli->close();

    // Default output matches common classmate format: raw list of users.
    // Optional compatibility format: api_users.php?format=wrapped
    $format = isset($_GET['format']) ? strtolower(trim((string) $_GET['format'])) : 'flat';
    if ($format === 'wrapped') {
        echo json_encode(array(
            'company'       => db_company_name(),
            'company_code'  => db_company_code(),
            'users'         => $users,
        ), JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode($users, JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'error'   => true,
        'message' => $e->getMessage(),
        'company' => @db_company_name(),
        'users'   => array(),
    ));
}
