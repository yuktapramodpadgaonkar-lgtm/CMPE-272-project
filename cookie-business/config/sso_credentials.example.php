<?php
/**
 * Copy to sso_credentials.local.php and adjust for your environment.
 * The app_secret must match OurMarketplace sso_apps.app_secret for app_id cookie-business.
 *
 * For local development, use:
 *   'provider_base' => 'http://localhost/CMPE-272-project/ourMarketplace',
 *   'redirect_url' => 'http://localhost/CMPE-272-project/cookie-business/sso/callback.php',
 */
return [
    'app_secret' => 'c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4',
    // 'provider_base' => 'http://localhost/CMPE-272-project/ourMarketplace',
    // 'redirect_url' => 'http://localhost/CMPE-272-project/cookie-business/sso/callback.php',
];
