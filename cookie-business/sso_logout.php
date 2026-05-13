<?php
require_once __DIR__ . '/includes/marketplace_sso.php';

marketplace_sso_clear();
header('Location: index.php', true, 302);
exit;
