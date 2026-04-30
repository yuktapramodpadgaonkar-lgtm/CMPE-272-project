<?php
$page_title = 'Users';
include 'includes/header.php';
?>
  <div class="container user-directory">
    <h1>User directory</h1>
    <p>Manage customer and staff contacts for Sweet Crumb Homemade Cookies.</p>
    <div class="user-hub-cards">
      <div class="card">
        <h3>Add a user</h3>
        <p>Capture first name, last name, email, address, home phone, and cell phone.</p>
        <a class="cookie-links" href="user_create.php">Open user creation form</a>
      </div>
      <div class="card">
        <h3>Search users</h3>
        <p>Find people by first or last name, email, home phone, or cell phone.</p>
        <a class="cookie-links" href="user_search.php">Open user search</a>
      </div>
    </div>
    <p class="muted small">Database: tables and seed users are installed via <code>sql/cmpe272_company_users.sql</code>.</p>
  </div>
<?php include 'includes/footer.php'; ?>
