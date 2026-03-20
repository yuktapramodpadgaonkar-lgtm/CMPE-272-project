<?php
require_once __DIR__ . '/includes/products_catalog.php';
require_once __DIR__ . '/includes/cookies_track.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = get_product_by_id($id);

if ($product === null) {
    header('HTTP/1.1 404 Not Found');
    $page_title = 'Product Not Found';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container"><h1>Product not found</h1><p><a href="products.php">Back to Products</a></p></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

track_product_page_visit($id);

$page_title = htmlspecialchars($product['name']);
include __DIR__ . '/includes/header.php';
?>
  <div class="container product-detail">
    <p class="breadcrumb"><a href="products.php">&larr; All products</a></p>
    <article class="product-article">
      <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-hero-img" width="800" height="500" loading="lazy">
      <h1><?php echo htmlspecialchars($product['name']); ?></h1>
      <p class="product-lead"><?php echo htmlspecialchars($product['short']); ?></p>
      <p><?php echo htmlspecialchars($product['description']); ?></p>
    </article>
  </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
