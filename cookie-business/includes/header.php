<?php
require_once __DIR__ . '/site_session.php';
require_once __DIR__ . '/marketplace_sso.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>Sweet Crumb Homemade Cookies</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php if (marketplace_sso_is_logged_in()): ?>
  <div class="container" style="background:#e8f4ea;border-bottom:1px solid #b8d4be;padding:0.5rem 0;font-size:0.9rem;">
    Signed in via <strong>OurMarketplace</strong> as <?php echo htmlspecialchars(marketplace_sso_display_name()); ?>.
    <a href="sso_logout.php">Sign out (company site only)</a>
  </div>
  <?php endif; ?>
  <?php if (site_user_is_logged_in()): ?>
  <div class="container" style="background:#fff8e6;border-bottom:1px solid #e8d4a8;padding:0.5rem 0;font-size:0.9rem;">
    Customer account: <strong><?php echo htmlspecialchars(site_user_full_name()); ?></strong>
    (<?php echo htmlspecialchars(site_username()); ?>)
    &middot; <a href="site_logout.php">Log out</a>
  </div>
  <?php endif; ?>
  <header>
    <div class="container">
      <div class="logo"><a href="index.php">Sweet Crumb Homemade Cookies</a></div>
      <nav>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="products.php">Products</a></li>
          <li><a href="news.php">News</a></li>
          <li><a href="contacts.php">Contacts</a></li>
          <li><a href="users.php">Users</a></li>
          <li><a href="combined_users.php">All companies’ users</a></li>
          <?php if (site_user_is_logged_in()): ?>
          <li><a href="site_logout.php">Log out</a></li>
          <?php else: ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
          <?php endif; ?>
          <li><a href="admin-login.php">Admin</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <main>
