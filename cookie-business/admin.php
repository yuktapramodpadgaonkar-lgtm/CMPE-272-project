<?php
require_once __DIR__ . '/includes/auth.php';
auth_require_admin();

$page_title = 'Admin – Users';
include __DIR__ . '/includes/header.php';

// This is the “secure section” content: list current users of the web site.
$users = [
    'Mary Smith',
    'John Wang',
    'Alex Bington',
    'Priya Kumar',
    'Daniel Lee'
];
?>
  <div class="container">
    <h1>Secure Section – Current Users</h1>
    <p>This page is only visible to the administrator (logged in as "<?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'admin'); ?>").</p>
    <h2>Registered / Current Users</h2>
    <ul>
      <?php foreach ($users as $u): ?>
        <li><?php echo htmlspecialchars($u); ?></li>
      <?php endforeach; ?>
    </ul>
    <p style="margin-top: 1rem;">
      <a href="logout.php">Log out</a>
    </p>
  </div>
<?php include __DIR__ . '/includes/footer.php'; ?>

