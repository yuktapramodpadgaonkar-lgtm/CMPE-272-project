<?php
$page_title = 'Recently Viewed Products';
include __DIR__ . '/includes/header.php';
?>
  <div class="container">
    <h1>Last 5 Previously Visited Products</h1>
    <p class="muted" id="recent-desc">Tracked in this browser as you open marketplace-backed cookie products.</p>
    <div id="recent-list"></div>
  </div>

  <script>
  (function () {
    if (typeof SCMarketplace === 'undefined') {
      document.getElementById('recent-desc').textContent = 'Marketplace client failed to load.';
      return;
    }

    function escapeHtml(s) {
      return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    var list = SCMarketplace.getRecentVisited(5);
    if (!list || !list.length) {
      document.getElementById('recent-desc').textContent = 'You have not viewed any marketplace cookie products yet.';
      return;
    }
    document.getElementById('recent-desc').textContent = 'The last ' + list.length + ' cookie products you opened from this browser.';
    var html = '<ul class="tracked-list">';
    list.forEach(function (p) {
      html += '<li class="tracked-item">'
        + '<a href="' + escapeHtml(p.href || ('product.php?id=' + encodeURIComponent(p.id || ''))) + '">'
        + '<img src="' + escapeHtml(p.image || '') + '" alt="" width="120" height="80" loading="lazy">'
        + '<span>' + escapeHtml(p.name || '') + '</span>'
        + '</a>'
        + '</li>';
    });
    html += '</ul>';
    document.getElementById('recent-list').innerHTML = html;
  })();
  </script>
<?php include __DIR__ . '/includes/footer.php'; ?>
