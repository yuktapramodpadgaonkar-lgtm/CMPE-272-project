<?php
/**
 * OurMarketplace SSO defaults for Sweet Crumb Homemade Cookies.
 * Override secrets and local URLs in config/sso_credentials.local.php.
 */
$defaults = [
    'provider_base' => 'https://mansiguptacs.com/ourmarketplace',
    'app_id' => 'cookie-business',
    'app_secret' => '',
    'redirect_url' => 'https://www.yukta-padgaonkar.com/CMPE-272-project/cookie-business/sso/callback.php',
];

$local = [];
$localPath = __DIR__ . '/sso_credentials.local.php';
if (is_readable($localPath)) {
    $loaded = require $localPath;
    if (is_array($loaded)) {
        $local = $loaded;
    }
}

return array_merge($defaults, $local);
