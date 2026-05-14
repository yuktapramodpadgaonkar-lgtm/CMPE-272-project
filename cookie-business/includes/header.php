<?php
require_once __DIR__ . '/site_user_auth.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/sso_client.php';

$current_page = basename($_SERVER['PHP_SELF'] ?? '', '.php');
if ($current_page === '') {
    $current_page = 'index';
}
auth_start_session();
$siteUser = sc_site_user();
$isUserLoggedIn = sc_site_user_is_logged_in();
$isAdmin = !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
$profileActivePages = ['account', 'user_login', 'user_register', 'user_dashboard', 'login', 'register', 'start', 'callback'];
$isProfileActive = in_array($current_page, $profileActivePages, true);
$ssoCfg = sc_sso_config();
$apiBase = rtrim((string) ($ssoCfg['provider_base'] ?? ''), '/') . '/api';
$jsPath = dirname(__DIR__) . '/js/marketplace.js';
$jsVer = is_file($jsPath) ? (string) filemtime($jsPath) : (string) time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>Sweet Crumb Homemade Cookies</title>
  <link rel="stylesheet" href="css/style.css">
  <script>
    window.SCBConfig = {
      apiBase: <?php echo json_encode($apiBase); ?>
    };
  </script>
  <script src="js/marketplace.js?v=<?php echo htmlspecialchars($jsVer); ?>"></script>
</head>
<body>
  <?php if (!empty($_SESSION['marketplace_token_pending_sync']) && is_string($_SESSION['marketplace_token_pending_sync'])): ?>
  <script>
    (function () {
      try { localStorage.setItem('marketplace_token', <?php echo json_encode($_SESSION['marketplace_token_pending_sync']); ?>); } catch (e) {}
      if (typeof SCMarketplace !== 'undefined' && SCMarketplace.resetAuthCache) {
        SCMarketplace.resetAuthCache();
      }
    })();
  </script>
  <?php unset($_SESSION['marketplace_token_pending_sync']); ?>
  <?php endif; ?>
  <?php if ($isUserLoggedIn && !empty($_SESSION['marketplace_access_token']) && is_string($_SESSION['marketplace_access_token'])): ?>
  <script>
    (function () {
      try {
        var token = <?php echo json_encode($_SESSION['marketplace_access_token']); ?>;
        if (token) {
          localStorage.setItem('marketplace_token', token);
          if (typeof SCMarketplace !== 'undefined' && SCMarketplace.resetAuthCache) {
            SCMarketplace.resetAuthCache();
          }
        }
      } catch (e) {}
    })();
  </script>
  <?php endif; ?>
  <?php if (!empty($_SESSION['clear_marketplace_token_js'])): ?>
  <script>
    (function () {
      try { localStorage.removeItem('marketplace_token'); } catch (e) {}
      if (typeof SCMarketplace !== 'undefined' && SCMarketplace.logout) {
        SCMarketplace.logout();
      }
    })();
  </script>
  <?php unset($_SESSION['clear_marketplace_token_js']); ?>
  <?php endif; ?>
  <?php if ($isUserLoggedIn): ?>
  <div class="container" style="background:#fff8e6;border-bottom:1px solid #e8d4a8;padding:0.5rem 0;font-size:0.9rem;">
    Signed in to <strong>Sweet Crumb Homemade Cookies</strong> as <?php echo htmlspecialchars((string) ($siteUser['full_name'] ?? '')); ?>.
    &middot; <a href="user_dashboard.php">Dashboard</a>
    &middot; <a href="user_logout.php">Log out</a>
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
          <li><a href="popular-products.php">Top 5</a></li>
          <li><a href="recent-products.php">Recent</a></li>
          <li><a href="news.php">News</a></li>
          <li><a href="contacts.php">Contacts</a></li>
          <li><a href="users.php">Users</a></li>
          <li><a href="combined_users.php">All companies’ users</a></li>
          <li><a href="account.php"<?php echo $isProfileActive ? ' aria-current="page"' : ''; ?>><?php echo $isUserLoggedIn ? htmlspecialchars((string) ($siteUser['full_name'] ?? 'Account')) : ($isAdmin ? 'Admin' : 'Account'); ?></a></li>
          <li><a href="admin-login.php">Admin</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <main>
