<?php
$page_title = 'Create account';
require_once __DIR__ . '/includes/site_session.php';

if (site_user_is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/includes/site_auth_store.php';
    $err = site_register_from_post($_POST);
    if ($err === null) {
        $success = true;
    } else {
        $error = $err;
    }
}

include 'includes/header.php';
?>
  <div class="container">
    <h1>Create account</h1>
    <p>Register with <strong>full name</strong>, <strong>username</strong>, <strong>email</strong>, and password — same fields as OurMarketplace.</p>

    <?php if ($success): ?>
      <p class="success-msg">Registration successful. <a href="login.php">Log in here</a>.</p>
    <?php else: ?>
      <?php if ($error !== ''): ?>
        <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>

      <form class="stacked-form auth-card" method="post" action="register.php" style="max-width:420px;">
        <div class="form-row">
          <label for="full_name">Full name <span class="req">*</span></label>
          <input type="text" id="full_name" name="full_name" required maxlength="100"
                 value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
        </div>
        <div class="form-row">
          <label for="username">Username <span class="req">*</span></label>
          <input type="text" id="username" name="username" required minlength="3" maxlength="50"
                 value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
        </div>
        <div class="form-row">
          <label for="email">Email <span class="req">*</span></label>
          <input type="email" id="email" name="email" required maxlength="100"
                 value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="form-row">
          <label for="password">Password <span class="req">*</span></label>
          <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password">
          <small class="muted">At least 6 characters.</small>
        </div>
        <div class="form-row">
          <label for="confirm_password">Confirm password <span class="req">*</span></label>
          <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Create account</button>
        </div>
      </form>
      <p class="cookie-links">Already have an account? <a href="login.php">Log in</a></p>
    <?php endif; ?>
  </div>
<?php include 'includes/footer.php'; ?>
