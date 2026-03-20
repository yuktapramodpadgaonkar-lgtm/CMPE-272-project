<?php
require_once __DIR__ . '/includes/products_catalog.php';
require_once __DIR__ . '/includes/cookies_track.php';

$page_title = 'Most Visited Products';
include __DIR__ . '/includes/header.php';

$topIds = sc_get_top_five_most_visited_ids();
$counts = sc_get_visit_counts();
?>
  <div class="container">
    <h1>Five Most Visited Products</h1>
    <p>Visit counts are stored in a cookie (<code>sc_product_visit_counts</code>) and updated each time you open a product page.</p>
    <p><a href="products.php">&larr; Back to Products</a></p>
    <?php if (empty($topIds)): ?>
      <p class="muted">No visit data yet. Browse some <a href="products.php">products</a> and come back—each page view increments that product’s count.</p>
    <?php else: ?>
      <ol class="tracked-list popular">
        <?php foreach ($topIds as $pid):
            $p = get_product_by_id($pid);
            if ($p === null) {
                continue;
            }
            $c = $counts[$pid] ?? 0;
        ?>
          <li class="tracked-item">
            <a href="product.php?id=<?php echo (int) $pid; ?>">
              <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" width="120" height="80" loading="lazy">
              <span><?php echo htmlspecialchars($p['name']); ?></span>
            </a>
            <span class="visit-badge"><?php echo (int) $c; ?> visit<?php echo $c === 1 ? '' : 's'; ?></span>
          </li>
        <?php endforeach; ?>
      </ol>
    <?php endif; ?>
  </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
