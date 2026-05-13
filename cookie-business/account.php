<?php
require_once __DIR__ . '/includes/site_user_auth.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/sso_client.php';

$siteUser = sc_site_user();
$isSiteUser = sc_site_user_is_logged_in();
auth_start_session();
$isAdmin = !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
$ssoError = isset($_GET['sso_error']) ? (string) $_GET['sso_error'] : '';

$page_title = 'Account';
require_once __DIR__ . '/includes/header.php';
?>
  <div class="container">
    <h1>Your account</h1>
    <?php if ($ssoError !== ''): ?>
      <p class="error-msg"><?php echo htmlspecialchars($ssoError); ?></p>
    <?php endif; ?>

    <?php if ($isSiteUser): ?>
      <p class="product-lead">You are signed in with <strong>OurMarketplace</strong>.</p>
    <?php elseif ($isAdmin): ?>
      <p class="product-lead">You are signed in as an administrator.</p>
    <?php else: ?>
      <p class="product-lead">Customer accounts use <strong>OurMarketplace</strong> single sign-on. Create an account there, then sign in here.</p>
    <?php endif; ?>

    <div class="card-grid">
      <?php if ($isSiteUser && $siteUser): ?>
        <div class="card">
          <h3>Customer dashboard</h3>
          <p>Welcome, <?php echo htmlspecialchars((string) ($siteUser['full_name'] ?? '')); ?>.</p>
          <p><a class="cookie-links" href="user_dashboard.php">Open dashboard</a></p>
        </div>
        <div class="card">
          <h3>Browse marketplace products</h3>
          <p>Your product views and reviews sync with the marketplace from this site.</p>
          <p><a class="cookie-links" href="products.php">Open products</a></p>
        </div>
        <div class="card">
          <h3>Logout</h3>
          <p>Sign out from this site. Your marketplace browser session may still stay active until you log out there too.</p>
          <p><a class="cookie-links" href="user_logout.php">Logout</a></p>
        </div>
      <?php elseif (!$isAdmin): ?>
        <div class="card">
          <h3>Sign in</h3>
          <p>Use the same OurMarketplace account for this cookie site.</p>
          <p><a class="cookie-links" href="<?php echo htmlspecialchars(sc_sso_authorize_url()); ?>">Sign in with Our Marketplace</a></p>
        </div>
        <div class="card">
          <h3>Create account</h3>
          <p>Register on OurMarketplace, then return here and use Sign in.</p>
          <p><a class="cookie-links" href="<?php echo htmlspecialchars(sc_sso_marketplace_register_url()); ?>">Create account on Our Marketplace</a></p>
        </div>
      <?php endif; ?>

      <?php if ($isAdmin): ?>
        <div class="card">
          <h3>Admin</h3>
          <p>Administrator access is separate from customer SSO.</p>
          <p><a class="cookie-links" href="admin.php">Open admin page</a></p>
        </div>
      <?php elseif (!$isSiteUser): ?>
        <div class="card">
          <h3>Admin login</h3>
          <p>Staff and administrators sign in here instead of the marketplace account flow.</p>
          <p><a class="cookie-links" href="admin-login.php">Admin login</a></p>
        </div>
      <?php endif; ?>

      <div class="card">
        <h3>OurMarketplace</h3>
        <p>Browse the shared marketplace in a new tab.</p>
        <p><a class="cookie-links" href="<?php echo htmlspecialchars(rtrim((string) (sc_sso_config()['provider_base'] ?? ''), '/') . '/'); ?>" target="_blank" rel="noopener">Open Our Marketplace</a></p>
      </div>
    </div>
  </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
