<?php
require_once __DIR__ . '/includes/site_user_auth.php';
require_once __DIR__ . '/includes/local_product_store.php';

$page_title = 'Products';
$method = (string) ($_GET['method'] ?? 'best_rated');
$allowedMethods = [
    'best_rated' => 'Best Rated',
    'most_visited' => 'Most Visited',
    'most_reviewed' => 'Most Reviewed',
];
if (!isset($allowedMethods[$method])) {
    $method = 'best_rated';
}

$siteUser = sc_site_user();
$siteUserId = isset($siteUser['id']) ? (int) $siteUser['id'] : null;
$db = sc_cookie_store_connect();
$topProducts = sc_cookie_fetch_top_products($db, $method, 5);
$recentProducts = sc_cookie_fetch_recent_products($db, $siteUserId, 5);
$allProducts = sc_cookie_fetch_all_products($db);

function sc_cookie_product_card(array $product, int $rank = 0): string
{
    $imgSrc = (string) ($product['image_url'] ?? '');
    $name = htmlspecialchars((string) ($product['name'] ?? 'Unnamed'));
    $desc = (string) ($product['description'] ?? '');
    if (strlen($desc) > 100) {
        $desc = substr($desc, 0, 97) . '...';
    }
    $desc = htmlspecialchars($desc);
    $price = '$' . number_format((float) ($product['price'] ?? 0), 2);
    $reviewCount = (int) ($product['review_count'] ?? 0);
    $avgRating = (float) ($product['avg_rating'] ?? 0);
    $rankHtml = $rank > 0
        ? '<span style="display:inline-block;background:#8b4513;color:#fff;border-radius:999px;padding:.15rem .5rem;font-size:.8rem;margin-right:.4rem;">#' . $rank . '</span>'
        : '';
    $imgHtml = $imgSrc !== ''
        ? '<img src="' . htmlspecialchars($imgSrc) . '" alt="' . $name . '" class="card-thumb" width="400" height="240" loading="lazy">'
        : '<div class="card-thumb" style="display:flex;align-items:center;justify-content:center;background:#f5efe8;color:#7c5a43;">No image</div>';

    return '<a href="product.php?id=' . (int) ($product['id'] ?? 0) . '" class="card product-card-link">'
        . $imgHtml
        . '<h3>' . $rankHtml . $name . '</h3>'
        . '<p>' . $desc . '</p>'
        . '<p class="muted" style="margin-top:.5rem;">'
        . htmlspecialchars($price) . ' &middot; '
        . htmlspecialchars(sc_cookie_render_stars($avgRating)) . ' (' . $reviewCount . ' reviews)'
        . '</p>'
        . '<span class="view-product">View details &rarr;</span>'
        . '</a>';
}

include __DIR__ . '/includes/header.php';
?>
  <div class="container">
    <h1>Products &amp; Services</h1>
    <p class="product-lead">This cookie catalog now loads directly from the Sweet Crumb database, so product pages, rankings, visits, and reviews continue working even when the marketplace APIs are unavailable.</p>

    <p class="cookie-links">
      <a href="recent-products.php">Last 5 previously visited products</a>
      &nbsp;|&nbsp;
      <a href="popular-products.php">Five top cookie products</a>
      &nbsp;|&nbsp;
      <a href="account.php">Account</a>
    </p>

    <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin:1rem 0 0;">
      <?php foreach ($allowedMethods as $key => $label): ?>
        <a href="products.php?method=<?php echo htmlspecialchars($key); ?>" class="btn btn-secondary<?php echo $method === $key ? ' active' : ''; ?>"><?php echo htmlspecialchars($label); ?></a>
      <?php endforeach; ?>
    </div>

    <h2>Top 5 cookie products</h2>
    <p class="muted">Sorted by <?php echo htmlspecialchars($allowedMethods[$method]); ?> using local Sweet Crumb data.</p>
    <?php if (!empty($topProducts)): ?>
      <div class="card-grid product-cards">
        <?php foreach ($topProducts as $index => $product): ?>
          <?php echo sc_cookie_product_card($product, $index + 1); ?>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="muted">No ranked products available yet.</p>
    <?php endif; ?>

    <h2>Recently viewed by you</h2>
    <p class="muted">
      <?php echo !empty($recentProducts) ? 'The last ' . count($recentProducts) . ' cookie products opened in this browser or signed-in session.' : 'Open a product to start building your recent list.'; ?>
    </p>
    <?php if (!empty($recentProducts)): ?>
      <div class="card-grid product-cards">
        <?php foreach ($recentProducts as $product): ?>
          <?php echo sc_cookie_product_card($product); ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <h2>All cookie products</h2>
    <p class="muted">Sweet Crumb catalog loaded from the local company database (<?php echo count($allProducts); ?> products).</p>
    <div class="card-grid product-cards">
      <?php foreach ($allProducts as $product): ?>
        <?php echo sc_cookie_product_card($product); ?>
      <?php endforeach; ?>
    </div>
  </div>
<?php
$db->close();
include __DIR__ . '/includes/footer.php';
?>
