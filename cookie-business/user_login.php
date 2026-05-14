<?php
require_once __DIR__ . '/includes/site_user_auth.php';
require_once __DIR__ . '/includes/site_auth_store.php';

if (sc_site_user_is_logged_in()) {
    header('Location: user_dashboard.php', true, 302);
    exit;
}

$error = '';
$registered = isset($_GET['registered']) && $_GET['registered'] === '1';
$page_title = 'Customer Login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = (string) (site_login_from_post($_POST) ?? '');
    if ($error === '') {
        header('Location: user_dashboard.php', true, 302);
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>
  <div class="container" style="max-width:32rem;">
    <h1>Customer Login</h1>
    <p class="product-lead">Sign in with your Sweet Crumb account to write reviews and see your activity on this site.</p>

    <?php if ($registered): ?>
      <p class="success-msg">Registration successful. You can log in now.</p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="user_login.php" class="card" style="padding:1.25rem;">
      <div class="form-row">
        <label for="username">Username or Email</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars((string) ($_POST['username'] ?? '')); ?>" required style="width:100%;max-width:28rem;padding:.55rem;">
      </div>
      <div class="form-row">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required style="width:100%;max-width:28rem;padding:.55rem;">
      </div>
      <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <p style="margin-top:1rem;">Do not have an account? <a href="user_register.php">Register here</a>.</p>
  </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
