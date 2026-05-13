<?php
$page_title = 'Products';
include 'includes/header.php';
?>
  <div class="container">
    <h1>Products &amp; Services</h1>
    <p class="product-lead">This cookie catalog is powered by <strong>OurMarketplace</strong>. Ratings, top 5 rankings, visits, and product details stay in sync with the marketplace.</p>

    <p class="cookie-links">
      <a href="recent-products.php">Last 5 previously visited products</a>
      &nbsp;|&nbsp;
      <a href="popular-products.php">Five top marketplace products</a>
      &nbsp;|&nbsp;
      <a href="account.php">Account</a>
    </p>

    <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin:1rem 0 0;">
      <button type="button" class="btn btn-secondary sc-ranking-tab active" data-method="best_rated">Best Rated</button>
      <button type="button" class="btn btn-secondary sc-ranking-tab" data-method="most_visited">Most Visited</button>
      <button type="button" class="btn btn-secondary sc-ranking-tab" data-method="most_reviewed">Most Reviewed</button>
    </div>

    <h2 id="top-cookie-heading">Top 5 cookie products</h2>
    <p class="muted" id="top-cookie-desc">Loading top products from marketplace...</p>
    <div id="top-cookie-container"></div>

    <h2>Recently viewed by you</h2>
    <p class="muted" id="recent-desc">Products you opened from this browser.</p>
    <div id="recent-container"></div>

    <h2 id="catalog-heading">All cookie products</h2>
    <p class="muted" id="catalog-desc">Loading marketplace catalog...</p>
    <div id="catalog-container"></div>
  </div>

  <script>
  (function () {
    if (typeof SCMarketplace === 'undefined') {
      document.getElementById('catalog-desc').textContent = 'Marketplace client failed to load.';
      return;
    }

    function escapeHtml(s) {
      return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function renderStars(avg) {
      var rounded = Math.round(Number(avg) || 0);
      var s = '';
      for (var i = 1; i <= 5; i++) s += (i <= rounded) ? '★' : '☆';
      return s;
    }

    function renderCard(mp, rank) {
      var imgSrc = mp.image_url || mp.image || '';
      var img = imgSrc
        ? '<img src="' + escapeHtml(imgSrc) + '" alt="' + escapeHtml(mp.name || '') + '" class="card-thumb" width="400" height="240" loading="lazy">'
        : '<div class="card-thumb" style="display:flex;align-items:center;justify-content:center;background:#f5efe8;color:#7c5a43;">No image</div>';
      var price = (mp.price != null && mp.price !== '') ? '$' + Number(mp.price).toFixed(2) : '';
      var desc = (mp.description || '').toString();
      if (desc.length > 100) desc = desc.substring(0, 97) + '...';
      var rankHtml = rank ? '<span style="display:inline-block;background:#8b4513;color:#fff;border-radius:999px;padding:.15rem .5rem;font-size:.8rem;margin-right:.4rem;">#' + rank + '</span>' : '';
      var reviewCount = parseInt(mp.review_count || 0, 10);
      var href = mp.href || ('product.php?id=' + encodeURIComponent(mp.id));
      return '<a href="' + href + '" class="card product-card-link">'
        + img
        + '<h3>' + rankHtml + escapeHtml(mp.name || 'Unnamed') + '</h3>'
        + '<p>' + escapeHtml(desc) + '</p>'
        + '<p class="muted" style="margin-top:.5rem;">' + escapeHtml(price) + ' &middot; ' + renderStars(mp.avg_rating || 0) + ' (' + reviewCount + ' reviews)</p>'
        + '<span class="view-product">View details &rarr;</span>'
        + '</a>';
    }

    function renderGrid(containerId, products, withRank) {
      var container = document.getElementById(containerId);
      if (!products || products.length === 0) {
        container.innerHTML = '<p class="muted">No products available right now.</p>';
        return;
      }
      var html = '<div class="card-grid product-cards">';
      for (var i = 0; i < products.length; i++) {
        html += renderCard(products[i], withRank ? (i + 1) : 0);
      }
      html += '</div>';
      container.innerHTML = html;
    }

    function methodLabel(method) {
      if (method === 'most_visited') return 'Most Visited';
      if (method === 'most_reviewed') return 'Most Reviewed';
      return 'Best Rated';
    }

    function loadTop(method) {
      var desc = document.getElementById('top-cookie-desc');
      desc.textContent = 'Loading top products (' + methodLabel(method) + ')...';
      SCMarketplace.getTopProducts({ company_id: SCMarketplace.COMPANY_ID, method: method, limit: 5 })
        .then(function (data) {
          var list = (data && data.products) || [];
          desc.textContent = 'Cookie company top 5 — ' + methodLabel(method) + '.';
          renderGrid('top-cookie-container', list, true);
        })
        .catch(function () {
          desc.textContent = 'Could not load top products right now.';
        });
    }

    var tabs = document.querySelectorAll('.sc-ranking-tab');
    tabs.forEach(function (btn) {
      btn.addEventListener('click', function () {
        tabs.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        loadTop(btn.getAttribute('data-method'));
      });
    });
    loadTop('best_rated');

    var recent = SCMarketplace.getRecentVisited(5);
    if (!recent || recent.length === 0) {
      document.getElementById('recent-desc').textContent = 'Click a product to start building your recent list.';
      document.getElementById('recent-container').innerHTML = '';
    } else {
      document.getElementById('recent-desc').textContent = 'The last ' + recent.length + ' cookie products you opened in this browser.';
      renderGrid('recent-container', recent, false);
    }

    SCMarketplace.loadProducts().then(function (data) {
      var products = (data && data.products) || [];
      document.getElementById('catalog-desc').textContent = 'Marketplace catalog for Sweet Crumb (' + products.length + ' products).';
      renderGrid('catalog-container', products, false);
    }).catch(function () {
      document.getElementById('catalog-desc').textContent = 'Could not load products from marketplace.';
    });
  })();
  </script>
<?php include 'includes/footer.php'; ?>
