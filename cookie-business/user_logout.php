<?php
require_once __DIR__ . '/includes/site_user_auth.php';

sc_site_user_logout();
header('Location: account.php', true, 302);
exit;
