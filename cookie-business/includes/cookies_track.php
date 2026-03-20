<?php
/**
 * Web cookie tracking for CMPE 272 lab:
 * - Last 5 previously visited product pages
 * - Visit counts per product (for "five most visited")
 *
 * Call track_product_page_visit($id) at the top of product.php BEFORE any HTML output.
 *
 * Uses setcookie() forms compatible with PHP 7.1+ (array options need PHP 7.3+).
 */

/** Cookie path = folder of current script (works in subdirectories on shared hosting). */
function sc_cookie_path() {
    $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/index.php';
    $dir = dirname(str_replace('\\', '/', $script));
    if ($dir === '/' || $dir === '.' || $dir === '') {
        return '/';
    }
    return rtrim($dir, '/') . '/';
}

/**
 * Send a cookie (works on PHP 7.1–8.x; avoids passing array to setcookie on PHP < 7.3).
 */
function sc_send_cookie($name, $value, $lifetimeSeconds) {
    $expires = time() + (int) $lifetimeSeconds;
    $path = sc_cookie_path();
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70300) {
        setcookie($name, $value, array(
            'expires'  => $expires,
            'path'     => $path,
            'secure'   => $secure,
            'httponly' => false,
            'samesite' => 'Lax',
        ));
    } else {
        // PHP 7.1 / 7.2: legacy signature (no SameSite)
        setcookie($name, $value, $expires, $path, '', $secure, false);
    }
}

function sc_get_recent_product_ids() {
    if (empty($_COOKIE['sc_recent_products'])) {
        return array();
    }
    $decoded = json_decode($_COOKIE['sc_recent_products'], true);
    if (!is_array($decoded)) {
        return array();
    }
    $out = array();
    foreach ($decoded as $v) {
        $id = (int) $v;
        if ($id >= 1 && $id <= 10) {
            $out[] = $id;
        }
    }
    return $out;
}

/**
 * Maintain last 5 unique product IDs, most recent first.
 */
function sc_update_recent_products($productId) {
    $productId = (int) $productId;
    if ($productId < 1 || $productId > 10) {
        return;
    }
    $list = sc_get_recent_product_ids();
    $filtered = array();
    foreach ($list as $id) {
        if ((int) $id !== $productId) {
            $filtered[] = $id;
        }
    }
    $list = $filtered;
    array_unshift($list, $productId);
    $list = array_slice($list, 0, 5);
    $encoded = json_encode($list);
    sc_send_cookie('sc_recent_products', $encoded, 60 * 60 * 24 * 30);
    $_COOKIE['sc_recent_products'] = $encoded;
}

function sc_get_visit_counts() {
    if (empty($_COOKIE['sc_product_visit_counts'])) {
        return array();
    }
    $decoded = json_decode($_COOKIE['sc_product_visit_counts'], true);
    if (!is_array($decoded)) {
        return array();
    }
    $counts = array();
    foreach ($decoded as $k => $v) {
        $id = (int) $k;
        if ($id >= 1 && $id <= 10) {
            $counts[$id] = max(0, (int) $v);
        }
    }
    return $counts;
}

/**
 * Increment visit count for a product (most visited tracking).
 */
function sc_increment_visit_count($productId) {
    $productId = (int) $productId;
    if ($productId < 1 || $productId > 10) {
        return;
    }
    $counts = sc_get_visit_counts();
    if (!isset($counts[$productId])) {
        $counts[$productId] = 0;
    }
    $counts[$productId]++;
    $encoded = json_encode($counts);
    sc_send_cookie('sc_product_visit_counts', $encoded, 60 * 60 * 24 * 365);
    $_COOKIE['sc_product_visit_counts'] = $encoded;
}

/**
 * Top 5 product IDs by visit count (ties: lower product id first).
 */
function sc_get_top_five_most_visited_ids() {
    $counts = sc_get_visit_counts();
    if (empty($counts)) {
        return array();
    }
    $pairs = array();
    foreach ($counts as $id => $c) {
        $pairs[] = array('id' => (int) $id, 'c' => (int) $c);
    }
    usort($pairs, function ($a, $b) {
        if ($b['c'] !== $a['c']) {
            if ($b['c'] > $a['c']) {
                return 1;
            }
            if ($b['c'] < $a['c']) {
                return -1;
            }
        }
        if ($a['id'] < $b['id']) {
            return -1;
        }
        if ($a['id'] > $b['id']) {
            return 1;
        }
        return 0;
    });
    $pairs = array_slice($pairs, 0, 5);
    $ids = array();
    foreach ($pairs as $p) {
        $ids[] = $p['id'];
    }
    return $ids;
}

function track_product_page_visit($productId) {
    sc_update_recent_products($productId);
    sc_increment_visit_count($productId);
}
