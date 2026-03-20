<?php
/**
 * MySQL connection for combined-users lab.
 * Requires includes/db_config.php (copy from db_config.example.php).
 */

function db_load_config() {
    $path = __DIR__ . '/db_config.php';
    if (!is_readable($path)) {
        return null;
    }
    $cfg = require $path;
    return is_array($cfg) ? $cfg : null;
}

/**
 * @return mysqli
 * @throws Exception
 */
function db_connect() {
    $cfg = db_load_config();
    if ($cfg === null) {
        throw new Exception('Database not configured. Copy includes/db_config.example.php to includes/db_config.php and edit.');
    }
    foreach (array('host', 'user', 'pass', 'dbname') as $k) {
        if (!isset($cfg[$k])) {
            throw new Exception('db_config.php is missing key: ' . $k);
        }
    }
    $mysqli = @new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['dbname']);
    if ($mysqli->connect_errno) {
        throw new Exception('MySQL connection failed: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}

function db_company_name() {
    $cfg = db_load_config();
    return isset($cfg['company_name']) ? $cfg['company_name'] : 'Unknown';
}

function db_company_code() {
    $cfg = db_load_config();
    return isset($cfg['company_code']) ? $cfg['company_code'] : '?';
}

function db_remote_apis() {
    $cfg = db_load_config();
    if (!isset($cfg['remote_apis']) || !is_array($cfg['remote_apis'])) {
        return array();
    }
    return $cfg['remote_apis'];
}
