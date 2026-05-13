<?php
require_once __DIR__ . '/includes/site_session.php';
site_user_clear_session();
header('Location: index.php', true, 302);
exit;
