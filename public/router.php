<?php
/**
 * Router script for the PHP built-in server (development only):
 *
 *   php -S localhost:8000 -t public public/router.php
 *
 * It serves existing static files directly and sends everything else to the
 * front controller, mirroring the production .htaccess rewrite.
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
