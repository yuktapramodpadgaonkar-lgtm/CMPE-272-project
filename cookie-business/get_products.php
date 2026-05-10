<?php
/**
 * Public JSON catalog for Cross-Domain Marketplace integration.
 * Expected by OurMarketplace (companies/view.php): JSON array of objects with
 * id, product_name, description, price, image_url (same contract as mgcodes.com/get_products.php).
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/includes/products_catalog.php';

/**
 * Wholesale-style prices for demo / marketplace sync (catalog site uses contact for custom quotes).
 */
function cookie_catalog_price(int $id): float {
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

function cookie_catalog_category(int $id): string {
    if ($id === 8) {
        return 'Catering';
    }
    if ($id === 9) {
        return 'Subscriptions';
    }
    return 'Cookies';
}

$catalog = get_products_catalog();
$out = [];
foreach ($catalog as $id => $item) {
    $pid = (int) $id;
    $desc = isset($item['description']) ? (string) $item['description'] : '';
    if ($desc === '' && isset($item['short'])) {
        $desc = (string) $item['short'];
    }
    $row = [
        'id'            => $pid,
        'product_name'  => isset($item['name']) ? (string) $item['name'] : '',
        'description'   => $desc,
        'price'         => cookie_catalog_price($pid),
        'image_url'     => isset($item['image']) ? (string) $item['image'] : '',
        'category'      => cookie_catalog_category($pid),
    ];
    $out[] = $row;
}

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
