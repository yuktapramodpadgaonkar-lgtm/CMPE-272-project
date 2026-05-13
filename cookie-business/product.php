<?php
require_once __DIR__ . '/includes/site_user_auth.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: products.php');
    exit;
}

$site_logged_in = sc_site_user_is_logged_in();
$page_title = 'Product';
include __DIR__ . '/includes/header.php';
?>
  <div class="container product-detail">
    <p class="breadcrumb"><a href="products.php">&larr; All products</a></p>

    <div id="mp-detail-status">
      <p class="muted">Loading product details...</p>
    </div>

    <article id="mp-detail-card" class="product-article" style="display:none;">
      <img id="mp-detail-image" alt="" class="product-hero-img" width="800" height="500" loading="eager">
      <h1 id="mp-detail-name"></h1>
      <p class="product-lead" id="mp-detail-meta"></p>
      <div id="mp-detail-description"></div>
    </article>

    <section id="mp-detail-reviews-section" style="display:none;margin-top:2rem;">
      <h2>Reviews <span id="mp-detail-reviews-count" class="muted"></span></h2>
      <div id="mp-detail-rating-breakdown" style="margin-bottom:1rem;"></div>
      <div id="mp-detail-reviews"></div>
    </section>

    <section id="mp-review-write-card" style="display:none;margin-top:2rem;" class="card">
      <h3>Write a review</h3>
      <p id="mp-review-write-hint" class="muted"></p>
      <form id="mp-review-form" style="display:none;">
        <div class="form-row">
          <label for="mp-review-rating">Rating <span class="req">*</span> (1-5)</label>
          <select id="mp-review-rating" name="rating" required style="max-width:12rem;padding:.4rem;">
            <option value="5">5 - Excellent</option>
            <option value="4">4</option>
            <option value="3">3</option>
            <option value="2">2</option>
            <option value="1">1</option>
          </select>
        </div>
        <div class="form-row">
          <label for="mp-review-text">Your review</label>
          <textarea id="mp-review-text" name="review_text" rows="4" style="width:100%;max-width:36rem;padding:.5rem;" placeholder="Share your experience..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary" id="mp-review-submit">Submit review</button>
        <p id="mp-review-msg" style="margin-top:.75rem;font-size:.9rem;" aria-live="polite"></p>
      </form>
    </section>
  </div>

  <script>
  (function () {
    var productId = <?php echo json_encode($id); ?>;
    var siteUserLoggedIn = <?php echo $site_logged_in ? 'true' : 'false'; ?>;

    if (typeof SCMarketplace === 'undefined') {
      document.getElementById('mp-detail-status').innerHTML = '<p class="error-msg">Marketplace client failed to load.</p>';
      return;
    }

    function escapeHtml(s) {
      return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function stars(n) {
      var rounded = Math.round(Number(n) || 0);
      var s = '';
      for (var i = 1; i <= 5; i++) s += (i <= rounded) ? '★' : '☆';
      return s;
    }

    function renderRatingBreakdown(breakdown, totalCount) {
      var box = document.getElementById('mp-detail-rating-breakdown');
      if (!breakdown || typeof breakdown !== 'object') {
        box.innerHTML = '';
        return;
      }
      var total = totalCount > 0 ? totalCount : 0;
      if (total === 0) {
        for (var k in breakdown) total += parseInt(breakdown[k] || 0, 10);
      }
      if (total === 0) {
        box.innerHTML = '';
        return;
      }
      var html = '<div style="display:flex;flex-direction:column;gap:.3rem;max-width:340px;">';
      ['5','4','3','2','1'].forEach(function (k) {
        var count = parseInt(breakdown[k] || 0, 10);
        var pct = total > 0 ? Math.round((count / total) * 100) : 0;
        html += '<div style="display:flex;align-items:center;gap:.5rem;font-size:.85rem;">'
          + '<span style="width:1.5rem;color:#64748b;">' + k + '★</span>'
          + '<div style="flex:1;height:.55rem;background:#f5efe8;border-radius:4px;overflow:hidden;"><div style="width:' + pct + '%;height:100%;background:#8b4513;"></div></div>'
          + '<span style="width:2.5rem;text-align:right;color:#64748b;">' + count + '</span>'
          + '</div>';
      });
      html += '</div>';
      box.innerHTML = html;
    }

    function renderReviews(reviews) {
      var section = document.getElementById('mp-detail-reviews-section');
      var countEl = document.getElementById('mp-detail-reviews-count');
      var reviewsEl = document.getElementById('mp-detail-reviews');
      section.style.display = 'block';
      if (!reviews || reviews.length === 0) {
        countEl.textContent = '(0)';
        reviewsEl.innerHTML = '<p class="muted">No reviews yet for this product.</p>';
        return;
      }
      countEl.textContent = '(' + reviews.length + ')';
      var html = '';
      reviews.forEach(function (rev) {
        html += '<div style="border-bottom:1px solid #e8d5c4;padding:.75rem 0;">'
          + '<div style="display:flex;justify-content:space-between;align-items:center;">'
          + '<strong>' + escapeHtml(rev.full_name || rev.username || 'Anonymous') + '</strong>'
          + '<span style="color:#8b4513;font-size:.95rem;">' + stars(rev.rating) + '</span>'
          + '</div>';
        if (rev.review_text) html += '<p style="margin:.35rem 0 0;color:#64748b;">' + escapeHtml(rev.review_text) + '</p>';
        if (rev.created_at) html += '<p style="margin:.2rem 0 0;font-size:.8rem;color:#64748b;">' + escapeHtml(rev.created_at) + '</p>';
        html += '</div>';
      });
      reviewsEl.innerHTML = html;
    }

    function setupReviewForm() {
      var writeCard = document.getElementById('mp-review-write-card');
      var form = document.getElementById('mp-review-form');
      var hint = document.getElementById('mp-review-write-hint');
      var msg = document.getElementById('mp-review-msg');
      writeCard.style.display = 'block';
      SCMarketplace.onAuthReady(function (mpUser) {
        if (!siteUserLoggedIn) {
          hint.innerHTML = 'Open <a href="account.php">Account</a> and sign in with Our Marketplace to post a review.';
          form.style.display = 'none';
          return;
        }
        if (!mpUser) {
          hint.innerHTML = 'Your marketplace token is missing. Open <a href="account.php">Account</a> and sign in again.';
          form.style.display = 'none';
          return;
        }
        hint.textContent = 'This review is stored on OurMarketplace and appears both here and on the marketplace.';
        form.style.display = 'block';
        form.addEventListener('submit', function (ev) {
          ev.preventDefault();
          msg.textContent = '';
          msg.style.color = '';
          var btn = document.getElementById('mp-review-submit');
          var rating = parseInt(document.getElementById('mp-review-rating').value, 10);
          var text = document.getElementById('mp-review-text').value;
          if (btn) btn.disabled = true;
          SCMarketplace.submitReview(productId, rating, text).then(function () {
            msg.textContent = 'Thank you! Your review was saved. Refreshing...';
            msg.style.color = '#8b4513';
            setTimeout(function () { location.reload(); }, 900);
          }).catch(function (err) {
            msg.textContent = (err && err.message) ? err.message : 'Could not submit review.';
            msg.style.color = '#b00020';
          }).finally(function () {
            if (btn) btn.disabled = false;
          });
        }, { once: true });
      });
    }

    setupReviewForm();

    SCMarketplace.loadProductDetail(productId).then(function (data) {
      if (!data || !data.product) {
        throw new Error('Unexpected marketplace response.');
      }
      var product = data.product;
      document.getElementById('mp-detail-status').style.display = 'none';
      document.getElementById('mp-detail-card').style.display = 'block';
      document.getElementById('mp-detail-image').src = product.image_url || '';
      document.getElementById('mp-detail-image').alt = product.name || '';
      document.getElementById('mp-detail-name').textContent = product.name || 'Unnamed product';
      document.getElementById('mp-detail-meta').innerHTML =
        ((product.price != null && product.price !== '') ? ('$' + Number(product.price).toFixed(2)) : '')
        + ' &middot; '
        + stars(product.avg_rating || 0)
        + ' '
        + (product.avg_rating ? Number(product.avg_rating).toFixed(1) + '/5' : 'Not rated')
        + ' <span class="muted">(' + parseInt(product.review_count || 0, 10) + ' reviews, ' + parseInt(product.visit_count || 0, 10) + ' visits)</span>';
      document.getElementById('mp-detail-description').innerHTML = '<p>' + escapeHtml(product.description || '') + '</p>';

      SCMarketplace.trackVisit(productId);
      SCMarketplace.recordVisit({
        id: product.id,
        href: 'product.php?id=' + encodeURIComponent(product.id),
        name: product.name,
        image: product.image_url || '',
        price: product.price,
        category: product.category || ''
      });

      renderRatingBreakdown(data.rating_breakdown || {}, parseInt(product.review_count || 0, 10));
      renderReviews(data.reviews || []);
    }).catch(function (err) {
      document.getElementById('mp-detail-status').innerHTML = '<p class="error-msg">' + escapeHtml((err && err.message) ? err.message : 'Could not load this product.') + '</p>';
    });
  })();
  </script>
<?php include __DIR__ . '/includes/footer.php'; ?>
