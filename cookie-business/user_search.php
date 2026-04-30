<?php
$page_title = 'Search users';
require_once __DIR__ . '/includes/users_store.php';

$q = isset($_GET['q']) ? $_GET['q'] : '';
list($results, $searchError) = users_search($q);

include 'includes/header.php';
?>
  <div class="container user-directory">
    <h1>Search users</h1>
    <p><a class="cookie-links" href="users.php">← Back to Users</a></p>

    <form class="stacked-form search-form" method="get" action="user_search.php">
      <div class="form-row inline-search">
        <label for="q">Search by name, email, or phone</label>
        <div class="search-bar">
          <input type="search" id="q" name="q" placeholder="e.g. Smith, 408, or mary@…"
                 value="<?php echo htmlspecialchars($q); ?>" maxlength="200">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </div>
    </form>

    <?php if ($searchError): ?>
      <p class="error-msg"><?php echo htmlspecialchars($searchError); ?></p>
    <?php elseif (trim($q) === ''): ?>
      <p class="muted">Enter a term above to search the directory.</p>
    <?php elseif (empty($results)): ?>
      <p class="muted">No users matched <strong><?php echo htmlspecialchars($q); ?></strong>.</p>
    <?php else: ?>
      <p class="muted small"><?php echo count($results); ?> result(s)</p>
      <div class="table-scroll">
        <table class="user-table wide">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Address</th>
              <th>Home</th>
              <th>Cell</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($results as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars(trim($row['first_name'] . ' ' . $row['last_name'])); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['home_address']); ?></td>
                <td><?php echo htmlspecialchars($row['home_phone']); ?></td>
                <td><?php echo htmlspecialchars($row['cell_phone']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
<?php include 'includes/footer.php'; ?>
