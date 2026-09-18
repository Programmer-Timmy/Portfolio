<?php
error_reporting(E_ERROR | E_PARSE);

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/controllers/Database.php';
require_once __DIR__ . '/controllers/Env.php';
require_once __DIR__ . '/controllers/Videos.php';

$summary = Videos::add();
echo "Added {$summary['added']}, updated {$summary['updated']}, deleted {$summary['deleted']}.\n";