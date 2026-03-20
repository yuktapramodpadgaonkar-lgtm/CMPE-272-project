<?php
require_once __DIR__ . '/includes/products_catalog.php';
$page_title = 'Products';
include 'includes/header.php';
$catalog = get_products_catalog();
?>
  <div class="container">
    <h1>Products &amp; Services</h1>
    <p>We offer ten cookie varieties and services—each has its own page with photos and details.</p>
    <p class="cookie-links">
      <a href="recent-products.php">Last 5 previously visited products</a>
      &nbsp;|&nbsp;
      <a href="popular-products.php">Five most visited products</a>
    </p>
    <h2>Our Products (10)</h2>
    <div class="card-grid product-cards">
      <?php foreach ($catalog as $id => $item): ?>
        <a href="product.php?id=<?php echo (int) $id; ?>" class="card product-card-link">
          <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="card-thumb" width="400" height="240" loading="lazy">
          <h3><?php echo htmlspecialchars($item['name']); ?></h3>
          <p><?php echo htmlspecialchars($item['short']); ?></p>
          <span class="view-product">View details &rarr;</span>
        </a>
      <?php endforeach; ?>
    </div>
    <h2>Services</h2>
    <ul>
      <li><strong>Custom orders:</strong> Mix and match flavors, quantities, and packaging.</li>
      <li><strong>Catering:</strong> Cookie trays and boxes for meetings, parties, and events.</li>
      <li><strong>Gift boxes:</strong> Beautifully packed boxes for shipping or local delivery.</li>
    </ul>
    <p>Contact us for pricing and availability: <a href="contacts.php">Contacts</a>.</p>
  </div>
<?php include 'includes/footer.php'; ?>
