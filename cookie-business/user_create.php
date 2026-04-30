<?php
$page_title = 'Create user';
require_once __DIR__ . '/includes/users_store.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $err = users_insert($_POST);
    if ($err === null) {
        $success = 'User saved successfully.';
    } else {
        $error = $err;
    }
}

include 'includes/header.php';
?>
  <div class="container user-directory">
    <h1>Create user</h1>
    <p><a class="cookie-links" href="users.php">← Back to Users</a></p>

    <?php if ($success !== ''): ?>
      <p class="success-msg"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
      <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form class="stacked-form" method="post" action="user_create.php" novalidate>
      <div class="form-row">
        <label for="first_name">First name <span class="req">*</span></label>
        <input type="text" id="first_name" name="first_name" required maxlength="100"
               value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
      </div>
      <div class="form-row">
        <label for="last_name">Last name <span class="req">*</span></label>
        <input type="text" id="last_name" name="last_name" required maxlength="100"
               value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
      </div>
      <div class="form-row">
        <label for="email">Email <span class="req">*</span></label>
        <input type="email" id="email" name="email" required maxlength="150"
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
      </div>
      <div class="form-row">
        <label for="home_address">Home address <span class="req">*</span></label>
        <textarea id="home_address" name="home_address" required rows="3" maxlength="255"><?php echo isset($_POST['home_address']) ? htmlspecialchars($_POST['home_address']) : ''; ?></textarea>
      </div>
      <div class="form-row">
        <label for="home_phone">Home phone</label>
        <input type="text" id="home_phone" name="home_phone" maxlength="40"
               value="<?php echo isset($_POST['home_phone']) ? htmlspecialchars($_POST['home_phone']) : ''; ?>">
      </div>
      <div class="form-row">
        <label for="cell_phone">Cell phone</label>
        <input type="text" id="cell_phone" name="cell_phone" maxlength="40"
               value="<?php echo isset($_POST['cell_phone']) ? htmlspecialchars($_POST['cell_phone']) : ''; ?>">
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save user</button>
      </div>
    </form>
  </div>
<?php include 'includes/footer.php'; ?>
