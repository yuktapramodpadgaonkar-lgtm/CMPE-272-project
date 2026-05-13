<?php
require_once __DIR__ . '/db.php';

/**
 * Find or create a site_users row for an OurMarketplace SSO identity.
 *
 * @return array{0:bool,1:?array,2:string}
 */
function sc_sso_upsert_site_user_from_marketplace(int $marketplaceUserId, string $username, string $fullName): array
{
    try {
        $db = db_connect();
    } catch (Exception $e) {
        return [false, null, 'Database is not configured.'];
    }

    $username = trim($username);
    $fullName = trim($fullName);
    if ($username === '') {
        $username = 'user' . $marketplaceUserId;
    }
    if ($fullName === '') {
        $fullName = $username;
    }

    $email = 'mp-' . $marketplaceUserId . '@sso.cookie-business.local';

    $stmt = $db->prepare('SELECT id, marketplace_user_id, username, full_name, email FROM site_users WHERE marketplace_user_id = ? LIMIT 1');
    if (!$stmt) {
        $db->close();
        return [false, null, 'Could not look up SSO user. Import sql/site_users.sql on this site.'];
    }
    $stmt->bind_param('i', $marketplaceUserId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    if ($row) {
        $id = (int) $row['id'];
        $upd = $db->prepare('UPDATE site_users SET username = ?, full_name = ?, last_logged_in = NOW() WHERE id = ?');
        if ($upd) {
            $upd->bind_param('ssi', $username, $fullName, $id);
            $upd->execute();
            $upd->close();
        }
        $db->close();
        return [true, [
            'id' => $id,
            'marketplace_user_id' => $marketplaceUserId,
            'username' => $username,
            'full_name' => $fullName,
            'email' => (string) $row['email'],
        ], ''];
    }

    $ins = $db->prepare('INSERT INTO site_users (marketplace_user_id, username, full_name, email, last_logged_in) VALUES (?, ?, ?, ?, NOW())');
    if (!$ins) {
        $db->close();
        return [false, null, 'Could not create SSO-linked site user. Import sql/site_users.sql on this site.'];
    }
    $ins->bind_param('isss', $marketplaceUserId, $username, $fullName, $email);
    if (!$ins->execute()) {
        $ins->close();
        $db->close();
        return [false, null, 'Could not create SSO-linked site user.'];
    }
    $newId = (int) $ins->insert_id;
    $ins->close();
    $db->close();

    return [true, [
        'id' => $newId,
        'marketplace_user_id' => $marketplaceUserId,
        'username' => $username,
        'full_name' => $fullName,
        'email' => $email,
    ], ''];
}
