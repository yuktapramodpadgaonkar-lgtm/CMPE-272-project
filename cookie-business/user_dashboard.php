<?php
require_once __DIR__ . '/includes/site_user_auth.php';

sc_require_site_user('user_login.php');
$user = sc_site_user();

$page_title = 'My Dashboard';
require_once __DIR__ . '/includes/header.php';
?>
  <div class="container">
    <h1>Welcome, <?php echo htmlspecialchars((string) ($user['full_name'] ?? '')); ?></h1>
    <p class="product-lead">You are signed in with your Sweet Crumb customer account.</p>

    <?php if (($_GET['welcome'] ?? '') === 'register'): ?>
      <p class="success-msg">Your customer account is ready to use.</p>
    <?php endif; ?>

    <div class="card-grid">
      <div class="card">
        <h3>Browse products</h3>
        <p>Open the cookie catalog stored in the Sweet Crumb database and post reviews directly on this site.</p>
        <p><a class="cookie-links" href="products.php">Open products</a></p>
      </div>
      <div class="card">
        <h3>Recent activity</h3>
        <p>See your recently viewed cookie products from this browser or signed-in session.</p>
        <p><a class="cookie-links" href="recent-products.php">Recent products</a></p>
      </div>
      <div class="card">
        <h3>Top products</h3>
        <p>See the cookie company top 5 by rating, visits, or reviews using local Sweet Crumb data.</p>
        <p><a class="cookie-links" href="popular-products.php">Top products</a></p>
      </div>
    </div>
  </div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
