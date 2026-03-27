<?php
/**
 * Combined list: local users (MySQL) + remote companies (cURL to their api_users.php).
 */
$page_title = 'All Companies Users';
require_once __DIR__ . '/includes/db.php';

/**
 * Fetch remote JSON from another company's api_users.php.
 *
 * @return array|null Decoded payload or null on failure
 */
function fetch_remote_users_json($url) {
    $url = trim($url);
    if ($url === '' || strpos($url, 'http') !== 0) {
        return null;
    }
    if (!function_exists('curl_init')) {
        return array('error' => 'PHP cURL extension is not enabled on this server.');
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($body === false) {
        return array('error' => 'cURL failed: ' . $err);
    }
    if ($code !== 200) {
        return array('error' => 'HTTP ' . $code . ' from ' . $url);
    }
    $data = json_decode($body, true);
    if (!is_array($data)) {
        return array('error' => 'Invalid JSON from ' . $url);
    }
    return $data;
}

/**
 * Returns true when payload is a list-style array (0..n keys), not an object map.
 */
function is_list_array($value) {
    if (!is_array($value)) {
        return false;
    }
    return array_keys($value) === range(0, count($value) - 1);
}

$localBlock = array('company' => '', 'company_code' => '', 'users' => array(), 'error' => '');
$remoteBlocks = array();

try {
    $mysqli = db_connect();
    $result = $mysqli->query('SELECT id, name, email FROM users ORDER BY id ASC');
    if ($result === false) {
        throw new Exception($mysqli->error);
    }
    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = array(
            'id'    => (int) $row['id'],
            'name'  => $row['name'],
            'email' => $row['email'],
        );
    }
    $mysqli->close();
    $localBlock['company'] = db_company_name();
    $localBlock['company_code'] = db_company_code();
    $localBlock['users'] = $users;
} catch (Exception $e) {
    $localBlock['error'] = $e->getMessage();
}

foreach (db_remote_apis() as $apiUrl) {
    $data = fetch_remote_users_json($apiUrl);
    if ($data === null) {
        $remoteBlocks[] = array(
            'url'   => $apiUrl,
            'error' => 'Invalid or empty API URL (must start with http).',
        );
        continue;
    }
    if (isset($data['error'])) {
        $remoteBlocks[] = array(
            'url'   => $apiUrl,
            'error' => is_string($data['error']) ? $data['error'] : 'Remote error',
        );
    } else {
        // Support both payload formats:
        // 1) {"company":"...","company_code":"...","users":[...]} (preferred)
        // 2) [{"id":1,"name":"...","email":"..."}] (fallback)
        $usersFromPayload = array();
        if (isset($data['users']) && is_array($data['users'])) {
            $usersFromPayload = $data['users'];
        } elseif (is_list_array($data)) {
            $usersFromPayload = $data;
        }

        $host = parse_url($apiUrl, PHP_URL_HOST);
        $remoteBlocks[] = array(
            'url'          => $apiUrl,
            'company'      => isset($data['company']) ? $data['company'] : ($host ? $host : 'Unknown'),
            'company_code' => isset($data['company_code']) ? $data['company_code'] : '?',
            'users'        => $usersFromPayload,
        );
    }
}

include __DIR__ . '/includes/header.php';
?>
  <div class="container combined-users">
    <h1>Combined list of users (all companies)</h1>
    <p>This page loads <strong>our</strong> users from the local MySQL database and <strong>other companies’</strong> users via <strong>cURL</strong> to their public <code>api_users.php</code> endpoints.</p>

    <section class="user-section">
      <h2>Local — <?php echo htmlspecialchars($localBlock['company']); ?> (<?php echo htmlspecialchars($localBlock['company_code']); ?>)</h2>
      <?php if ($localBlock['error'] !== ''): ?>
        <p class="error-msg"><?php echo htmlspecialchars($localBlock['error']); ?></p>
      <?php else: ?>
        <table class="user-table">
          <thead><tr><th>ID</th><th>Name</th><th>Email</th></tr></thead>
          <tbody>
            <?php foreach ($localBlock['users'] as $u): ?>
              <tr>
                <td><?php echo (int) $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>

    <?php foreach ($remoteBlocks as $block): ?>
      <section class="user-section">
        <?php if (isset($block['error'])): ?>
          <h2>Remote (cURL)</h2>
          <p class="error-msg"><strong>URL:</strong> <?php echo htmlspecialchars($block['url']); ?><br><?php echo htmlspecialchars($block['error']); ?></p>
        <?php else: ?>
          <h2>Remote — <?php echo htmlspecialchars($block['company']); ?> (<?php echo htmlspecialchars($block['company_code']); ?>)</h2>
          <p class="muted small">Source: <code><?php echo htmlspecialchars($block['url']); ?></code></p>
          <?php if (empty($block['users'])): ?>
            <p class="muted">No users in response.</p>
          <?php else: ?>
            <table class="user-table">
              <thead><tr><th>ID</th><th>Name</th><th>Email</th></tr></thead>
              <tbody>
                <?php foreach ($block['users'] as $u): ?>
                  <tr>
                    <td><?php echo isset($u['id']) ? (int) $u['id'] : ''; ?></td>
                    <td><?php echo htmlspecialchars(isset($u['name']) ? $u['name'] : ''); ?></td>
                    <td><?php echo htmlspecialchars(isset($u['email']) ? $u['email'] : ''); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        <?php endif; ?>
      </section>
    <?php endforeach; ?>

    <?php if (empty(db_remote_apis())): ?>
      <p class="muted">No remote API URLs configured yet. Edit <code>includes/db_config.php</code> → <code>remote_apis</code> with your teammates’ full <code>api_users.php</code> URLs.</p>
    <?php endif; ?>
  </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
