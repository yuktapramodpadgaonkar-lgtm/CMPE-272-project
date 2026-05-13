<?php
$page_title = 'Top Products';
include __DIR__ . '/includes/header.php';
?>
  <div class="container">
    <h1>Top 5 Cookie Products</h1>
    <p class="muted" id="top-desc">Loading rankings from OurMarketplace...</p>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin:1rem 0 0;">
      <button type="button" class="btn btn-secondary sc-top-tab active" data-method="best_rated">Best Rated</button>
      <button type="button" class="btn btn-secondary sc-top-tab" data-method="most_visited">Most Visited</button>
      <button type="button" class="btn btn-secondary sc-top-tab" data-method="most_reviewed">Most Reviewed</button>
    </div>
    <div id="top-list"></div>
  </div>

  <script>
  (function () {
    if (typeof SCMarketplace === 'undefined') {
      document.getElementById('top-desc').textContent = 'Marketplace client failed to load.';
      return;
    }

    function escapeHtml(s) {
      return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function render(method) {
      var desc = document.getElementById('top-desc');
      desc.textContent = 'Loading ' + method.replace('_', ' ') + '...';
      SCMarketplace.getTopProducts({ company_id: SCMarketplace.COMPANY_ID, method: method, limit: 5 })
        .then(function (data) {
          var list = (data && data.products) || [];
          desc.textContent = 'Showing ' + list.length + ' cookie products from OurMarketplace.';
          if (!list.length) {
            document.getElementById('top-list').innerHTML = '<p class="muted">No ranked products yet.</p>';
            return;
          }
          var html = '<ol class="tracked-list popular">';
          list.forEach(function (p) {
            html += '<li class="tracked-item">'
              + '<a href="product.php?id=' + encodeURIComponent(p.id) + '">'
              + '<img src="' + escapeHtml(p.image_url || '') + '" alt="" width="120" height="80" loading="lazy">'
              + '<span>' + escapeHtml(p.name || '') + '</span>'
              + '</a>'
              + '<span class="visit-badge">' + escapeHtml(method === 'most_visited' ? ((p.visit_count || 0) + ' visits') : (method === 'most_reviewed' ? ((p.review_count || 0) + ' reviews') : (Number(p.avg_rating || 0).toFixed(1) + '/5'))) + '</span>'
              + '</li>';
          });
          html += '</ol>';
          document.getElementById('top-list').innerHTML = html;
        })
        .catch(function () {
          desc.textContent = 'Could not load top products right now.';
        });
    }

    var tabs = document.querySelectorAll('.sc-top-tab');
    tabs.forEach(function (btn) {
      btn.addEventListener('click', function () {
        tabs.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        render(btn.getAttribute('data-method'));
      });
    });
    render('best_rated');
  })();
  </script>
<?php include __DIR__ . '/includes/footer.php'; ?>
