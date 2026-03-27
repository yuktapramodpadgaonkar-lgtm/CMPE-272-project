<?php
/**
 * Copy this file to db_config.php on your server and fill in real values.
 * Command: cp includes/db_config.example.php includes/db_config.php
 *
 * Do not commit db_config.php if it contains passwords (use .gitignore).
 */
return array(
    'host'   => '127.0.0.1',
    'user'   => 'root',
    'pass'   => '',
    'dbname' => 'cmpe272_company_users',

    // Shown in JSON API and combined page
    'company_name' => 'Sweet Crumb Homemade Cookies',
    'company_code' => 'SCHC',

    /**
     * Full HTTPS URLs to teammates' api_users.php (Company B and C).
     * Example:
     * 'https://classmate-b.com/CMPE-272-project/cookie-business/api_users.php',
     * 'https://classmate-c.com/CMPE-272-project/cookie-business/api_users.php',
     */
    'remote_apis' => array(
        'http://geekyhub.me/api/users.php',
    ),
);
