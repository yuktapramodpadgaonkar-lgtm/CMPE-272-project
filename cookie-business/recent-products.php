<?php
require_once __DIR__ . '/includes/site_user_auth.php';
require_once __DIR__ . '/includes/local_product_store.php';

$page_title = 'Recently Viewed Products';
$siteUser = sc_site_user();
$siteUserId = isset($siteUser['id']) ? (int) $siteUser['id'] : null;
$db = sc_cookie_store_connect();
$products = sc_cookie_fetch_recent_products($db, $siteUserId, 5);

include __DIR__ . '/includes/header.php';
?>
  <div class="container">
    <h1>Last 5 Previously Visited Products</h1>
    <p class="muted">
      <?php echo !empty($products) ? 'The last ' . count($products) . ' cookie products opened from this browser or signed-in session.' : 'You have not viewed any local cookie products yet.'; ?>
    </p>

    <?php if (!empty($products)): ?>
      <ul class="tracked-list">
        <?php foreach ($products as $product): ?>
          <li class="tracked-item">
            <a href="product.php?id=<?php echo (int) ($product['id'] ?? 0); ?>">
              <img src="<?php echo htmlspecialchars((string) ($product['image_url'] ?? '')); ?>" alt="" width="120" height="80" loading="lazy">
              <span><?php echo htmlspecialchars((string) ($product['name'] ?? '')); ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
<?php
$db->close();
include __DIR__ . '/includes/footer.php';
?>
