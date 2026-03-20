<?php
require_once __DIR__ . '/includes/products_catalog.php';
require_once __DIR__ . '/includes/cookies_track.php';

$page_title = 'Recently Viewed Products';
include __DIR__ . '/includes/header.php';

$ids = sc_get_recent_product_ids();
?>
  <div class="container">
    <h1>Last 5 Previously Visited Products</h1>
    <p>Tracked with a web cookie (<code>sc_recent_products</code>) as you browse individual product pages.</p>
    <p><a href="products.php">&larr; Back to Products</a></p>
    <?php if (empty($ids)): ?>
      <p class="muted">You have not viewed any product pages yet. Open a few products from the <a href="products.php">Products</a> page, then return here.</p>
    <?php else: ?>
      <ul class="tracked-list">
        <?php foreach ($ids as $pid):
            $p = get_product_by_id($pid);
            if ($p === null) {
                continue;
            }
        ?>
          <li class="tracked-item">
            <a href="product.php?id=<?php echo (int) $pid; ?>">
              <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" width="120" height="80" loading="lazy">
              <span><?php echo htmlspecialchars($p['name']); ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
