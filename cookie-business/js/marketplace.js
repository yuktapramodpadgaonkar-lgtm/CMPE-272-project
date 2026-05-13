/**
 * OurMarketplace API client for Sweet Crumb Homemade Cookies.
 * company_id = 3
 */
var SCMarketplace = (function () {
    var CONFIG = (window.SCBConfig || {});
    var API_BASE = CONFIG.apiBase || 'https://mansiguptacs.com/ourmarketplace/api';
    var COMPANY_ID = 3;
    var TOKEN_KEY = 'marketplace_token';
    var RECENT_KEY = 'sc_visited_products';
    var RECENT_MAX = 50;

    function fetchJSON(url, opts) {
        return fetch(url, opts || {}).then(function (r) {
            return r.text().then(function (txt) {
                var data;
                try { data = JSON.parse(txt); } catch (e) { data = null; }
                return { ok: r.ok, status: r.status, data: data, raw: txt };
            });
        });
    }

    function loadProducts() {
        return fetchJSON(API_BASE + '/products.php?company_id=' + COMPANY_ID)
            .then(function (r) { return r.data; });
    }

    function loadProductDetail(productId) {
        return fetchJSON(API_BASE + '/product_detail.php?id=' + productId)
            .then(function (r) { return r.data; });
    }

    function loadReviews(productId) {
        return fetchJSON(API_BASE + '/reviews.php?product_id=' + productId)
            .then(function (r) { return r.data; });
    }

    function submitReview(productId, rating, reviewText) {
        var token = localStorage.getItem(TOKEN_KEY);
        if (!token) {
            return Promise.reject(new Error('Sign in with Our Marketplace first.'));
        }
        var rid = parseInt(productId, 10);
        var stars = Math.round(parseFloat(rating));
        if (rid <= 0 || stars < 1 || stars > 5) {
            return Promise.reject(new Error('Invalid product or rating.'));
        }
        return fetchJSON(API_BASE + '/reviews.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-Marketplace-Token': token
            },
            body: JSON.stringify({
                product_id: rid,
                rating: stars,
                review_text: (reviewText == null ? '' : String(reviewText)).trim(),
                access_token: token
            })
        }).then(function (r) {
            if (!r.ok) {
                var msg = (r.data && r.data.error) ? r.data.error : ('HTTP ' + r.status);
                throw new Error(msg);
            }
            return r.data;
        });
    }

    function verify() {
        var token = localStorage.getItem(TOKEN_KEY);
        if (!token) return Promise.resolve(null);
        return fetchJSON(API_BASE + '/verify.php', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'X-Marketplace-Token': token
            }
        }).then(function (r) {
            if (r.ok && r.data && r.data.logged_in) {
                return r.data.user;
            }
            localStorage.removeItem(TOKEN_KEY);
            return null;
        }).catch(function () {
            return null;
        });
    }

    function logout() {
        localStorage.removeItem(TOKEN_KEY);
        _authPromise = null;
        _authUser = undefined;
    }

    function resetAuthCache() {
        _authPromise = null;
        _authUser = undefined;
    }

    function getToken() {
        return localStorage.getItem(TOKEN_KEY);
    }

    var _authPromise = null;
    var _authUser = undefined;

    function onAuthReady(cb) {
        if (!_authPromise) {
            _authPromise = verify().then(function (user) {
                _authUser = user || null;
                return _authUser;
            }).catch(function () {
                _authUser = null;
                return null;
            });
        }
        if (typeof cb === 'function') _authPromise.then(cb);
        return _authPromise;
    }

    function trackVisit(productId) {
        if (productId == null) return Promise.resolve(null);
        var headers = { 'Content-Type': 'application/json' };
        var token = localStorage.getItem(TOKEN_KEY);
        if (token) headers['Authorization'] = 'Bearer ' + token;
        return fetchJSON(API_BASE + '/track_visit.php', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ product_id: productId })
        }).then(function (r) { return r.data; })
          .catch(function () { return null; });
    }

    function getTopProducts(opts) {
        opts = opts || {};
        var params = [];
        if (opts.company_id) params.push('company_id=' + encodeURIComponent(opts.company_id));
        if (opts.method) params.push('method=' + encodeURIComponent(opts.method));
        if (opts.limit) params.push('limit=' + encodeURIComponent(opts.limit));
        var url = API_BASE + '/top_products.php' + (params.length ? '?' + params.join('&') : '');
        return fetchJSON(url).then(function (r) { return r.data; });
    }

    function readRecentStore() {
        try {
            var raw = localStorage.getItem(RECENT_KEY);
            if (!raw) return [];
            var arr = JSON.parse(raw);
            return Array.isArray(arr) ? arr : [];
        } catch (e) {
            return [];
        }
    }

    function writeRecentStore(arr) {
        try {
            localStorage.setItem(RECENT_KEY, JSON.stringify(arr.slice(0, RECENT_MAX)));
        } catch (e) {}
    }

    function makeEntryKey(entry) {
        if (entry.key) return entry.key;
        if (entry.id != null) return 'mp:' + entry.id;
        return null;
    }

    function recordVisit(entry) {
        if (!entry) return null;
        var key = makeEntryKey(entry);
        if (!key) return null;

        var store = readRecentStore();
        var now = Date.now();
        var existingIdx = -1;
        for (var i = 0; i < store.length; i++) {
            if (store[i].key === key) {
                existingIdx = i;
                break;
            }
        }

        var record;
        if (existingIdx >= 0) {
            record = store[existingIdx];
            record.count = (parseInt(record.count, 10) || 0) + 1;
            record.lastVisited = now;
            if (entry.id != null) record.id = entry.id;
            if (entry.name) record.name = entry.name;
            if (entry.image) record.image = entry.image;
            if (entry.price != null) record.price = entry.price;
            if (entry.category) record.category = entry.category;
            if (entry.href) record.href = entry.href;
            store.splice(existingIdx, 1);
        } else {
            record = {
                key: key,
                id: entry.id != null ? entry.id : null,
                href: entry.href || '',
                name: entry.name || '',
                image: entry.image || '',
                price: entry.price != null ? entry.price : null,
                category: entry.category || '',
                count: 1,
                lastVisited: now
            };
        }
        store.unshift(record);
        writeRecentStore(store);
        return record;
    }

    function getRecentVisited(n) {
        var store = readRecentStore();
        store.sort(function (a, b) { return (b.lastVisited || 0) - (a.lastVisited || 0); });
        return n ? store.slice(0, n) : store.slice();
    }

    return {
        loadProducts: loadProducts,
        loadProductDetail: loadProductDetail,
        loadReviews: loadReviews,
        submitReview: submitReview,
        verify: verify,
        logout: logout,
        resetAuthCache: resetAuthCache,
        getToken: getToken,
        onAuthReady: onAuthReady,
        trackVisit: trackVisit,
        getTopProducts: getTopProducts,
        recordVisit: recordVisit,
        getRecentVisited: getRecentVisited,
        API_BASE: API_BASE,
        COMPANY_ID: COMPANY_ID
    };
})();
