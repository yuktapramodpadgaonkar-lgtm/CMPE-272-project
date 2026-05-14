<?php
require_once __DIR__ . '/includes/site_user_auth.php';
require_once __DIR__ . '/includes/local_product_store.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: products.php');
    exit;
}

$siteUser = sc_site_user();
$siteLoggedIn = sc_site_user_is_logged_in();
$siteUserId = $siteLoggedIn ? (int) ($siteUser['id'] ?? 0) : null;
$reviewError = '';

$db = sc_cookie_store_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$siteLoggedIn || $siteUserId <= 0) {
        $reviewError = 'Sign in from the Account page to post a review.';
    } else {
        $rating = (int) ($_POST['rating'] ?? 0);
        $reviewText = (string) ($_POST['review_text'] ?? '');
        if ($rating < 1 || $rating > 5) {
            $reviewError = 'Choose a rating from 1 to 5.';
        } else {
            $ok = sc_cookie_upsert_review($db, $id, $siteUserId, $rating, $reviewText);
            if ($ok) {
                $db->close();
                header('Location: product.php?id=' . $id . '&review_saved=1');
                exit;
            }
            $reviewError = 'Could not save your review right now.';
        }
    }
}

$product = sc_cookie_fetch_product($db, $id);
if (!$product) {
    $db->close();
    header('Location: products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sc_cookie_track_product_visit($db, $id, $siteUserId);
    $product = sc_cookie_fetch_product($db, $id) ?: $product;
}

$reviews = sc_cookie_fetch_reviews($db, $id);
$ratingBreakdown = sc_cookie_rating_breakdown($reviews);
$reviewSaved = isset($_GET['review_saved']) && $_GET['review_saved'] === '1';

$page_title = (string) ($product['name'] ?? 'Product');
include __DIR__ . '/includes/header.php';
?>
  <div class="container product-detail">
    <p class="breadcrumb"><a href="products.php">&larr; All products</a></p>

    <article class="product-article">
      <img src="<?php echo htmlspecialchars((string) ($product['image_url'] ?? '')); ?>" alt="<?php echo htmlspecialchars((string) ($product['name'] ?? '')); ?>" class="product-hero-img" width="800" height="500" loading="eager">
      <h1><?php echo htmlspecialchars((string) ($product['name'] ?? '')); ?></h1>
      <p class="product-lead">
        $<?php echo number_format((float) ($product['price'] ?? 0), 2); ?>
        &middot;
        <?php echo htmlspecialchars(sc_cookie_render_stars((float) ($product['avg_rating'] ?? 0))); ?>
        <?php if ((float) ($product['avg_rating'] ?? 0) > 0): ?>
          <?php echo number_format((float) ($product['avg_rating'] ?? 0), 1); ?>/5
        <?php else: ?>
          Not rated
        <?php endif; ?>
        <span class="muted">(<?php echo (int) ($product['review_count'] ?? 0); ?> reviews, <?php echo (int) ($product['visit_count'] ?? 0); ?> visits)</span>
      </p>
      <p class="muted">Category: <?php echo htmlspecialchars((string) ($product['category'] ?? 'Cookies')); ?></p>
      <div>
        <p><?php echo htmlspecialchars((string) ($product['description'] ?? '')); ?></p>
      </div>
    </article>

    <section style="margin-top:2rem;">
      <h2>Reviews <span class="muted">(<?php echo count($reviews); ?>)</span></h2>
      <?php if (!empty($reviews)): ?>
        <div style="display:flex;flex-direction:column;gap:.3rem;max-width:340px;margin-bottom:1rem;">
          <?php foreach (['5', '4', '3', '2', '1'] as $ratingKey): ?>
            <?php
            $count = (int) ($ratingBreakdown[$ratingKey] ?? 0);
            $pct = count($reviews) > 0 ? (int) round(($count / count($reviews)) * 100) : 0;
            ?>
            <div style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;">
              <span style="width:1.5rem;color:#64748b;"><?php echo $ratingKey; ?>★</span>
              <div style="flex:1;height:.55rem;background:#f5efe8;border-radius:4px;overflow:hidden;">
                <div style="width:<?php echo $pct; ?>%;height:100%;background:#8b4513;"></div>
              </div>
              <span style="width:2.5rem;text-align:right;color:#64748b;"><?php echo $count; ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (empty($reviews)): ?>
        <p class="muted">No reviews yet for this product.</p>
      <?php else: ?>
        <?php foreach ($reviews as $review): ?>
          <div style="border-bottom:1px solid #e8d5c4;padding:.75rem 0;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;">
              <strong><?php echo htmlspecialchars((string) ($review['full_name'] ?? $review['username'] ?? 'Anonymous')); ?></strong>
              <span style="color:#8b4513;font-size:.95rem;"><?php echo htmlspecialchars(sc_cookie_render_stars((float) ($review['rating'] ?? 0))); ?></span>
            </div>
            <?php if (!empty($review['review_text'])): ?>
              <p style="margin:.35rem 0 0;color:#64748b;"><?php echo htmlspecialchars((string) $review['review_text']); ?></p>
            <?php endif; ?>
            <?php if (!empty($review['created_at'])): ?>
              <p style="margin:.2rem 0 0;font-size:.8rem;color:#64748b;"><?php echo htmlspecialchars((string) $review['created_at']); ?></p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

    <section style="margin-top:2rem;" class="card">
      <h3>Write a review</h3>
      <?php if ($reviewSaved): ?>
        <p class="success-msg">Your review was saved successfully.</p>
      <?php endif; ?>
      <?php if ($reviewError !== ''): ?>
        <p class="error-msg"><?php echo htmlspecialchars($reviewError); ?></p>
      <?php endif; ?>

      <?php if (!$siteLoggedIn): ?>
        <p class="muted">Open <a href="account.php">Account</a> and sign in to post a review on this cookie site.</p>
      <?php else: ?>
        <p class="muted">This review is stored in the Sweet Crumb database and appears on this site immediately.</p>
        <form method="post" action="product.php?id=<?php echo $id; ?>">
          <div class="form-row">
            <label for="review-rating">Rating <span class="req">*</span> (1-5)</label>
            <select id="review-rating" name="rating" required style="max-width:12rem;padding:.4rem;">
              <option value="5">5 - Excellent</option>
              <option value="4">4</option>
              <option value="3">3</option>
              <option value="2">2</option>
              <option value="1">1</option>
            </select>
          </div>
          <div class="form-row">
            <label for="review-text">Your review</label>
            <textarea id="review-text" name="review_text" rows="4" style="width:100%;max-width:36rem;padding:.5rem;" placeholder="Share your experience..."><?php echo htmlspecialchars((string) ($_POST['review_text'] ?? '')); ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit review</button>
        </form>
      <?php endif; ?>
    </section>
  </div>
<?php
$db->close();
include __DIR__ . '/includes/footer.php';
?>
