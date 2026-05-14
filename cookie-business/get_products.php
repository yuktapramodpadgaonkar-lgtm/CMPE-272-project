<?php
/**
 * Public JSON catalog for Cross-Domain Marketplace integration.
 * Expected by OurMarketplace (companies/view.php): JSON array of objects with
 * id, product_name, description, price, image_url (same contract as mgcodes.com/get_products.php).
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/includes/local_product_store.php';

$db = sc_cookie_store_connect();
$catalog = sc_cookie_fetch_all_products($db);
$out = [];
foreach ($catalog as $item) {
    $pid = (int) ($item['id'] ?? 0);
    $desc = (string) ($item['description'] ?? '');
    $row = [
        'id'            => $pid,
        'product_name'  => (string) ($item['name'] ?? ''),
        'description'   => $desc,
        'price'         => (float) ($item['price'] ?? 0),
        'image_url'     => (string) ($item['image_url'] ?? ''),
        'category'      => (string) ($item['category'] ?? ''),
    ];
    $out[] = $row;
}

$db->close();
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
