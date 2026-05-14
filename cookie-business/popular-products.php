<?php
require_once __DIR__ . '/includes/local_product_store.php';

$page_title = 'Top Products';
$method = (string) ($_GET['method'] ?? 'best_rated');
$allowedMethods = [
    'best_rated' => 'Best Rated',
    'most_visited' => 'Most Visited',
    'most_reviewed' => 'Most Reviewed',
];
if (!isset($allowedMethods[$method])) {
    $method = 'best_rated';
}

$db = sc_cookie_store_connect();
$products = sc_cookie_fetch_top_products($db, $method, 5);

include __DIR__ . '/includes/header.php';
?>
  <div class="container">
    <h1>Top 5 Cookie Products</h1>
    <p class="muted">Showing local Sweet Crumb rankings sorted by <?php echo htmlspecialchars($allowedMethods[$method]); ?>.</p>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin:1rem 0 0;">
      <?php foreach ($allowedMethods as $key => $label): ?>
        <a href="popular-products.php?method=<?php echo htmlspecialchars($key); ?>" class="btn btn-secondary<?php echo $method === $key ? ' active' : ''; ?>"><?php echo htmlspecialchars($label); ?></a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
      <p class="muted">No ranked products yet.</p>
    <?php else: ?>
      <ol class="tracked-list popular">
        <?php foreach ($products as $product): ?>
          <li class="tracked-item">
            <a href="product.php?id=<?php echo (int) ($product['id'] ?? 0); ?>">
              <img src="<?php echo htmlspecialchars((string) ($product['image_url'] ?? '')); ?>" alt="" width="120" height="80" loading="lazy">
              <span><?php echo htmlspecialchars((string) ($product['name'] ?? '')); ?></span>
            </a>
            <span class="visit-badge">
              <?php if ($method === 'most_visited'): ?>
                <?php echo (int) ($product['visit_count'] ?? 0); ?> visits
              <?php elseif ($method === 'most_reviewed'): ?>
                <?php echo (int) ($product['review_count'] ?? 0); ?> reviews
              <?php else: ?>
                <?php echo number_format((float) ($product['avg_rating'] ?? 0), 1); ?>/5
              <?php endif; ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ol>
    <?php endif; ?>
  </div>
<?php
$db->close();
include __DIR__ . '/includes/footer.php';
?>
