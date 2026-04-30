<?php
/**
 * User directory persistence (Sweet Crumb company users DB).
 */

require_once __DIR__ . '/db.php';

function users_full_name($first_name, $last_name) {
    return trim(trim((string) $first_name) . ' ' . trim((string) $last_name));
}

/**
 * Insert a directory user row. Computes display name column for API parity.
 *
 * @return string|null Error message or null on success
 */
function users_insert(array $fields) {
    $fn = isset($fields['first_name']) ? trim($fields['first_name']) : '';
    $ln = isset($fields['last_name']) ? trim($fields['last_name']) : '';
    $em = isset($fields['email']) ? trim($fields['email']) : '';
    $addr = isset($fields['home_address']) ? trim($fields['home_address']) : '';
    $hp = isset($fields['home_phone']) ? trim($fields['home_phone']) : '';
    $cp = isset($fields['cell_phone']) ? trim($fields['cell_phone']) : '';

    if ($fn === '' || $ln === '' || $em === '' || $addr === '') {
        return 'First name, last name, email, and home address are required.';
    }

    $name = users_full_name($fn, $ln);
    try {
        $mysqli = db_connect();
        $sql = 'INSERT INTO users (first_name, last_name, email, home_address, home_phone, cell_phone, name)
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $mysqli->prepare($sql);
        if ($stmt === false) {
            return 'Database prepare failed.';
        }
        $stmt->bind_param('sssssss', $fn, $ln, $em, $addr, $hp, $cp, $name);
        $ok = $stmt->execute();
        if (!$ok) {
            $dup = ($stmt->errno === 1062) || stripos($stmt->error, 'Duplicate') !== false;
            $stmt->close();
            $mysqli->close();
            return $dup ? 'That email is already registered.' : 'Could not save user.';
        }
        $stmt->close();
        $mysqli->close();
    } catch (Exception $e) {
        return $e->getMessage();
    }

    return null;
}

/**
 * Search by name pieces, email, or phone fragments.
 *
 * @return array{0:array,1:?string} rows keyed as DB columns plus error message
 */
function users_search($q) {
    $q = isset($q) ? trim((string) $q) : '';
    $rows = array();
    try {
        $mysqli = db_connect();

        // Backward-compat: old schema might not have new columns yet.
        $check = @$mysqli->query("SHOW COLUMNS FROM users LIKE 'first_name'");
        if (!$check || $check->num_rows === 0) {
            return array(array(), 'User directory columns are missing. Import sql/migrate_users_directory.sql or sql/cmpe272_company_users.sql.');
        }
        $check->close();

        if ($q === '') {
            $mysqli->close();
            return array(array(), null);
        }

        $like = '%' . $q . '%';
        $digits = preg_replace('/\D+/', '', $q);
        $likeDigits = $digits !== '' ? '%' . $digits . '%' : null;

        $p1 = $like;
        $p2 = $like;
        $p3 = $like;
        $p4 = $like;
        $p5 = $like;
        $p6 = $like;

        $sql = 'SELECT id, first_name, last_name, name, email, home_address, home_phone, cell_phone, created_at
                FROM users WHERE
                  first_name LIKE ? OR last_name LIKE ? OR name LIKE ? OR email LIKE ?
                  OR home_phone LIKE ? OR cell_phone LIKE ?';

        $types = 'ssssss';

        if ($likeDigits !== null) {
            $sql .= ' OR REPLACE(REPLACE(REPLACE(REPLACE(home_phone, \'-\', \'\' ), \'(\', \'\' ), \')\', \'\' ), \' \', \'\' )
                        LIKE ?
                    OR REPLACE(REPLACE(REPLACE(REPLACE(cell_phone, \'-\', \'\' ), \'(\', \'\' ), \')\', \'\' ), \' \', \'\' )
                        LIKE ?';
            $types .= 'ss';
        }

        $sql .= ' ORDER BY last_name ASC, first_name ASC';

        $stmt = $mysqli->prepare($sql);
        if ($stmt === false) {
            $mysqli->close();
            return array(array(), 'Search failed.');
        }

        if ($likeDigits !== null) {
            $d1 = $likeDigits;
            $d2 = $likeDigits;
            $stmt->bind_param($types, $p1, $p2, $p3, $p4, $p5, $p6, $d1, $d2);
        } else {
            $stmt->bind_param($types, $p1, $p2, $p3, $p4, $p5, $p6);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        $mysqli->close();
    } catch (Exception $e) {
        return array(array(), $e->getMessage());
    }

    return array($rows, null);
}
