<?php
require_once __DIR__ . '/includes/auth.php';
auth_start_session();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both userid and password.';
    } elseif (auth_check_credentials($username, $password)) {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_user'] = $username;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Invalid userid or password.';
    }
}

$page_title = 'Admin Login';
include __DIR__ . '/includes/header.php';
?>
  <div class="container">
    <h1>Administrator Login</h1>
    <p>Secure section – only the administrator can log in.</p>
    <?php if ($error !== ''): ?>
      <p style="color: #b91c1c; margin-top: 1rem;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="admin-login.php" style="margin-top: 1.5rem; max-width: 380px;">
      <div style="margin-bottom: 0.75rem; text-align: left;">
        <label for="username" style="display:block; font-weight:bold; margin-bottom:0.25rem;">User ID</label>
        <input type="text" id="username" name="username" required
               style="width:100%; padding:0.4rem; border:1px solid #e8d5c4; border-radius:4px;">
      </div>
      <div style="margin-bottom: 0.75rem; text-align: left;">
        <label for="password" style="display:block; font-weight:bold; margin-bottom:0.25rem;">Password</label>
        <input type="password" id="password" name="password" required
               style="width:100%; padding:0.4rem; border:1px solid #e8d5c4; border-radius:4px;">
      </div>
      <button type="submit"
              style="margin-top:0.5rem; padding:0.5rem 1.2rem; border:none; border-radius:4px; background:#8b4513; color:#fdf8f3; font-weight:bold; cursor:pointer;">
        Log in
      </button>
    </form>
    <p style="margin-top: 1rem; font-size: 0.9rem; color:#5c4033;">
      Hint for testing: userid <strong>admin</strong>, password <strong>admin123</strong>.
    </p>
  </div>
<?php include __DIR__ . '/includes/footer.php'; ?>

