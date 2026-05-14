<?php
require_once __DIR__ . '/includes/site_user_auth.php';
require_once __DIR__ . '/includes/site_auth_store.php';

if (sc_site_user_is_logged_in()) {
    header('Location: user_dashboard.php', true, 302);
    exit;
}

$error = '';
$page_title = 'Create Account';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = (string) (site_register_from_post($_POST) ?? '');
    if ($error === '') {
        header('Location: user_login.php?registered=1', true, 302);
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>
  <div class="container" style="max-width:36rem;">
    <h1>Create Customer Account</h1>
    <p class="product-lead">Create a local Sweet Crumb account for customer login on this website.</p>

    <?php if ($error !== ''): ?>
      <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="user_register.php" class="card" style="padding:1.25rem;">
      <div class="form-row">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars((string) ($_POST['full_name'] ?? '')); ?>" required style="width:100%;max-width:30rem;padding:.55rem;">
      </div>
      <div class="form-row">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars((string) ($_POST['username'] ?? '')); ?>" required style="width:100%;max-width:30rem;padding:.55rem;">
      </div>
      <div class="form-row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? '')); ?>" required style="width:100%;max-width:30rem;padding:.55rem;">
      </div>
      <div class="form-row">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required style="width:100%;max-width:30rem;padding:.55rem;">
      </div>
      <div class="form-row">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required style="width:100%;max-width:30rem;padding:.55rem;">
      </div>
      <button type="submit" class="btn btn-primary">Create Account</button>
    </form>

    <p style="margin-top:1rem;">Already have an account? <a href="user_login.php">Log in here</a>.</p>
  </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
