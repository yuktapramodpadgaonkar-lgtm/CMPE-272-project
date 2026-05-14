<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/products_catalog.php';

function sc_cookie_catalog_price(int $id): float
{
    $prices = [
        1  => 14.99,
        2  => 13.99,
        3  => 29.99,
        4  => 12.49,
        5  => 15.49,
        6  => 13.49,
        7  => 14.49,
        8  => 89.99,
        9  => 34.99,
        10 => 17.49,
    ];

    return isset($prices[$id]) ? $prices[$id] : 0.00;
}

function sc_cookie_catalog_category(int $id): string
{
    if ($id === 8) {
        return 'Catering';
    }

    if ($id === 9) {
        return 'Subscriptions';
    }

    return 'Cookies';
}

function sc_cookie_render_stars(float $avgRating): string
{
    $rounded = (int) round($avgRating);
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rounded ? '★' : '☆';
    }

    return $stars;
}

function sc_cookie_store_connect(): mysqli
{
    $db = db_connect();
    sc_cookie_store_bootstrap($db);
    return $db;
}

function sc_cookie_store_bootstrap(mysqli $db): void
{
    $db->query("
        CREATE TABLE IF NOT EXISTS site_users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            marketplace_user_id INT UNSIGNED NOT NULL UNIQUE,
            username VARCHAR(50) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            last_logged_in DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $db->query("
        CREATE TABLE IF NOT EXISTS cookie_products (
            id INT UNSIGNED NOT NULL PRIMARY KEY,
            slug VARCHAR(120) NOT NULL UNIQUE,
            name VARCHAR(150) NOT NULL,
            short_description VARCHAR(255) NOT NULL DEFAULT '',
            description TEXT NOT NULL,
            price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            image_url VARCHAR(255) NOT NULL DEFAULT '',
            category VARCHAR(100) NOT NULL DEFAULT 'Cookies',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $db->query("
        CREATE TABLE IF NOT EXISTS cookie_product_visits (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id INT UNSIGNED NOT NULL,
            site_user_id INT UNSIGNED NULL,
            session_key VARCHAR(128) NOT NULL DEFAULT '',
            visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            KEY idx_cookie_product_visits_product (product_id),
            KEY idx_cookie_product_visits_user (site_user_id),
            KEY idx_cookie_product_visits_session (session_key),
            CONSTRAINT fk_cookie_product_visits_product
                FOREIGN KEY (product_id) REFERENCES cookie_products(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_cookie_product_visits_user
                FOREIGN KEY (site_user_id) REFERENCES site_users(id)
                ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $db->query("
        CREATE TABLE IF NOT EXISTS cookie_product_reviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id INT UNSIGNED NOT NULL,
            site_user_id INT UNSIGNED NOT NULL,
            rating TINYINT UNSIGNED NOT NULL,
            review_text TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_cookie_product_reviews_user_product (product_id, site_user_id),
            KEY idx_cookie_product_reviews_product (product_id),
            KEY idx_cookie_product_reviews_user (site_user_id),
            CONSTRAINT fk_cookie_product_reviews_product
                FOREIGN KEY (product_id) REFERENCES cookie_products(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_cookie_product_reviews_user
                FOREIGN KEY (site_user_id) REFERENCES site_users(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    sc_cookie_store_seed_products($db);
}

function sc_cookie_store_seed_products(mysqli $db): void
{
    $catalog = get_products_catalog();
    $stmt = $db->prepare("
        INSERT INTO cookie_products (id, slug, name, short_description, description, price, image_url, category)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            slug = VALUES(slug),
            name = VALUES(name),
            short_description = VALUES(short_description),
            description = VALUES(description),
            price = VALUES(price),
            image_url = VALUES(image_url),
            category = VALUES(category)
    ");

    if (!$stmt) {
        return;
    }

    foreach ($catalog as $id => $product) {
        $pid = (int) $id;
        $slug = (string) ($product['slug'] ?? ('product-' . $pid));
        $name = (string) ($product['name'] ?? '');
        $short = (string) ($product['short'] ?? '');
        $description = (string) ($product['description'] ?? $short);
        $price = sc_cookie_catalog_price($pid);
        $imageUrl = (string) ($product['image'] ?? '');
        $category = sc_cookie_catalog_category($pid);
        $stmt->bind_param('issssdss', $pid, $slug, $name, $short, $description, $price, $imageUrl, $category);
        $stmt->execute();
    }

    $stmt->close();
}

function sc_cookie_current_session_key(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $sessionId = session_id();
    return $sessionId !== '' ? $sessionId : 'guest';
}

function sc_cookie_fetch_all_products(mysqli $db): array
{
    $sql = "
        SELECT p.*,
               COALESCE(rv.avg_rating, 0) AS avg_rating,
               COALESCE(rv.review_count, 0) AS review_count,
               COALESCE(vv.visit_count, 0) AS visit_count
        FROM cookie_products p
        LEFT JOIN (
            SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS review_count
            FROM cookie_product_reviews
            GROUP BY product_id
        ) rv ON rv.product_id = p.id
        LEFT JOIN (
            SELECT product_id, COUNT(*) AS visit_count
            FROM cookie_product_visits
            GROUP BY product_id
        ) vv ON vv.product_id = p.id
        ORDER BY p.id
    ";
    $result = $db->query($sql);
    if (!$result) {
        return [];
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

function sc_cookie_fetch_product(mysqli $db, int $productId): ?array
{
    $stmt = $db->prepare("
        SELECT p.*,
               COALESCE(rv.avg_rating, 0) AS avg_rating,
               COALESCE(rv.review_count, 0) AS review_count,
               COALESCE(vv.visit_count, 0) AS visit_count
        FROM cookie_products p
        LEFT JOIN (
            SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS review_count
            FROM cookie_product_reviews
            GROUP BY product_id
        ) rv ON rv.product_id = p.id
        LEFT JOIN (
            SELECT product_id, COUNT(*) AS visit_count
            FROM cookie_product_visits
            GROUP BY product_id
        ) vv ON vv.product_id = p.id
        WHERE p.id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function sc_cookie_fetch_top_products(mysqli $db, string $method = 'best_rated', int $limit = 5): array
{
    $allowed = [
        'best_rated' => 'avg_rating DESC, review_count DESC, visit_count DESC, p.name ASC',
        'most_visited' => 'visit_count DESC, avg_rating DESC, review_count DESC, p.name ASC',
        'most_reviewed' => 'review_count DESC, avg_rating DESC, visit_count DESC, p.name ASC',
    ];

    $orderBy = $allowed[$method] ?? $allowed['best_rated'];
    $limit = max(1, min(10, $limit));

    $sql = "
        SELECT p.*,
               COALESCE(rv.avg_rating, 0) AS avg_rating,
               COALESCE(rv.review_count, 0) AS review_count,
               COALESCE(vv.visit_count, 0) AS visit_count
        FROM cookie_products p
        LEFT JOIN (
            SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS review_count
            FROM cookie_product_reviews
            GROUP BY product_id
        ) rv ON rv.product_id = p.id
        LEFT JOIN (
            SELECT product_id, COUNT(*) AS visit_count
            FROM cookie_product_visits
            GROUP BY product_id
        ) vv ON vv.product_id = p.id
        ORDER BY {$orderBy}
        LIMIT {$limit}
    ";
    $result = $db->query($sql);
    if (!$result) {
        return [];
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

function sc_cookie_track_product_visit(mysqli $db, int $productId, ?int $siteUserId = null): void
{
    $sessionKey = sc_cookie_current_session_key();
    $stmt = $db->prepare("
        INSERT INTO cookie_product_visits (product_id, site_user_id, session_key)
        VALUES (?, ?, ?)
    ");

    if (!$stmt) {
        return;
    }

    $stmt->bind_param('iis', $productId, $siteUserId, $sessionKey);
    $stmt->execute();
    $stmt->close();
}

function sc_cookie_fetch_recent_products(mysqli $db, ?int $siteUserId, int $limit = 5): array
{
    $limit = max(1, min(10, $limit));
    $sessionKey = sc_cookie_current_session_key();

    if ($siteUserId !== null && $siteUserId > 0) {
        $stmt = $db->prepare("
            SELECT p.*,
                   COALESCE(rv.avg_rating, 0) AS avg_rating,
                   COALESCE(rv.review_count, 0) AS review_count,
                   COALESCE(vv.visit_count, 0) AS visit_count,
                   recent.last_visited
            FROM (
                SELECT product_id, MAX(visited_at) AS last_visited
                FROM cookie_product_visits
                WHERE site_user_id = ?
                GROUP BY product_id
                ORDER BY last_visited DESC
                LIMIT {$limit}
            ) recent
            JOIN cookie_products p ON p.id = recent.product_id
            LEFT JOIN (
                SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS review_count
                FROM cookie_product_reviews
                GROUP BY product_id
            ) rv ON rv.product_id = p.id
            LEFT JOIN (
                SELECT product_id, COUNT(*) AS visit_count
                FROM cookie_product_visits
                GROUP BY product_id
            ) vv ON vv.product_id = p.id
            ORDER BY recent.last_visited DESC
        ");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $siteUserId);
    } else {
        $stmt = $db->prepare("
            SELECT p.*,
                   COALESCE(rv.avg_rating, 0) AS avg_rating,
                   COALESCE(rv.review_count, 0) AS review_count,
                   COALESCE(vv.visit_count, 0) AS visit_count,
                   recent.last_visited
            FROM (
                SELECT product_id, MAX(visited_at) AS last_visited
                FROM cookie_product_visits
                WHERE session_key = ?
                GROUP BY product_id
                ORDER BY last_visited DESC
                LIMIT {$limit}
            ) recent
            JOIN cookie_products p ON p.id = recent.product_id
            LEFT JOIN (
                SELECT product_id, AVG(rating) AS avg_rating, COUNT(*) AS review_count
                FROM cookie_product_reviews
                GROUP BY product_id
            ) rv ON rv.product_id = p.id
            LEFT JOIN (
                SELECT product_id, COUNT(*) AS visit_count
                FROM cookie_product_visits
                GROUP BY product_id
            ) vv ON vv.product_id = p.id
            ORDER BY recent.last_visited DESC
        ");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('s', $sessionKey);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    return $rows;
}

function sc_cookie_fetch_reviews(mysqli $db, int $productId): array
{
    $stmt = $db->prepare("
        SELECT r.id, r.rating, r.review_text, r.created_at, u.full_name, u.username
        FROM cookie_product_reviews r
        JOIN site_users u ON u.id = r.site_user_id
        WHERE r.product_id = ?
        ORDER BY r.updated_at DESC, r.created_at DESC
    ");

    if (!$stmt) {
        return [];
    }

    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    return $rows;
}

function sc_cookie_rating_breakdown(array $reviews): array
{
    $breakdown = ['5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0];
    foreach ($reviews as $review) {
        $rating = (string) max(1, min(5, (int) ($review['rating'] ?? 0)));
        if (isset($breakdown[$rating])) {
            $breakdown[$rating]++;
        }
    }

    return $breakdown;
}

function sc_cookie_upsert_review(mysqli $db, int $productId, int $siteUserId, int $rating, string $reviewText): bool
{
    $rating = max(1, min(5, $rating));
    $reviewText = trim($reviewText);

    $stmt = $db->prepare("
        INSERT INTO cookie_product_reviews (product_id, site_user_id, rating, review_text)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            rating = VALUES(rating),
            review_text = VALUES(review_text),
            updated_at = CURRENT_TIMESTAMP
    ");

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('iiis', $productId, $siteUserId, $rating, $reviewText);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}
