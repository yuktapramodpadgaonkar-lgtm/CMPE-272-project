<?php
$page_title = 'Customer login';
require_once __DIR__ . '/includes/site_session.php';

if (site_user_is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/includes/site_auth_store.php';
    $err = site_login_from_post($_POST);
    if ($err === null) {
        header('Location: index.php');
        exit;
    }
    $error = $err;
}

include 'includes/header.php';
?>
  <div class="container">
    <h1>Customer login</h1>
    <p>Sign in with your <strong>username</strong> or <strong>email</strong> (same style as OurMarketplace).</p>

    <?php if ($error !== ''): ?>
      <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form class="stacked-form auth-card" method="post" action="login.php" style="max-width:420px;">
      <div class="form-row">
        <label for="username">Username or email</label>
        <input type="text" id="username" name="username" required autocomplete="username"
               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
      </div>
      <div class="form-row">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Log in</button>
      </div>
    </form>
    <p class="cookie-links"><a href="register.php">Create an account</a></p>
  </div>
<?php include 'includes/footer.php'; ?>
