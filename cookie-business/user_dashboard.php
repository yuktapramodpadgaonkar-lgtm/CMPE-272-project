<?php
require_once __DIR__ . '/includes/site_user_auth.php';

sc_require_site_user('sso/start.php');
$user = sc_site_user();

$page_title = 'My Dashboard';
require_once __DIR__ . '/includes/header.php';
?>
  <div class="container">
    <h1>Welcome, <?php echo htmlspecialchars((string) ($user['full_name'] ?? '')); ?></h1>
    <p class="product-lead">You are signed in with OurMarketplace on Sweet Crumb Homemade Cookies.</p>

    <?php if (($_GET['welcome'] ?? '') === 'sso'): ?>
      <p class="success-msg">Marketplace sign-in completed successfully.</p>
    <?php endif; ?>

    <div class="card-grid">
      <div class="card">
        <h3>Browse products</h3>
        <p>Open the marketplace-backed cookie catalog and post reviews that sync with the main marketplace.</p>
        <p><a class="cookie-links" href="products.php">Open products</a></p>
      </div>
      <div class="card">
        <h3>Recent activity</h3>
        <p>See your recently viewed marketplace products from this browser.</p>
        <p><a class="cookie-links" href="recent-products.php">Recent products</a></p>
      </div>
      <div class="card">
        <h3>Top products</h3>
        <p>See the cookie company top 5 by rating, visits, or reviews from the marketplace.</p>
        <p><a class="cookie-links" href="popular-products.php">Top products</a></p>
      </div>
    </div>
  </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
