<?php
// Include necessary files
require_once __DIR__ . '/../private/autoload.php';
require_once __DIR__ . '/../private/config/settings.php';
require_once __DIR__ . '/../private/routes.php';

// Start a session
session_start();

// Global variables
global $site;
global $database;
global $allowedIPs;

// Determine which page to display based on the request
$requestedPage = $_SERVER['REQUEST_URI'];
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if ($requestedPage == "/") {
    $requestedPage = '/home';
}

// remove the get parameters from the url
$position = strpos($requestedPage, "?");
$require = $requestedPage;
if ($position !== false) {
    $newString = substr($requestedPage, 0, $position);
    $require = $newString;
}

// if ajax is enabled and the request is an ajax request load the ajax file
if ($site['ajax']) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        include "../private/ajax/$require.php";
        exit();
    }
}

if ($site['admin']['enabled']) {
    $admin = $site['admin'];
    $pageTemplate = __DIR__ . "/../private/views/pages$require.php";

    if (str_contains($require, $admin['filterInUrl']) && $require !== $site['redirect'] && $require !== '/404' && $require !== '/maintenance') {
        if (file_exists($pageTemplate) || Router::isRoute($uri, true)) {
            if (!isset($_SESSION[$admin['sessionName']])) {
                if ($site['saveUrl']) {
                    $_SESSION['redirect'] = $requestedPage;
                }
                header('Location:/' . $site['redirect']);
                exit();
            }
        } else {
            header('Location:/404');
            exit();
        }
    }
}

if ($site['accounts']['enabled']) {
    $accounts = $site['accounts'];

    $pageTemplate = __DIR__ . "/../private/views/pages$require.php";

    if (str_contains($require, $accounts['filterInUrl']) && $require !== '/' . $site['redirect'] && $require !== '/404' && $require !== '/maintenance') {
        if (file_exists($pageTemplate) || Router::isRoute($uri, true)) {
            if (!isset($_SESSION[$accounts['sessionName']])) {
                if ($site['saveUrl']) {
                    if ($require !== '/' . $site['redirect']) {
                        $_SESSION['redirect'] = $require;
                    }
                }

                header('Location:/' . $site['redirect']);
                exit();
            }
        }
    }
}

// Special case for sitemap - needs to run before headers
if ($require === '/sitemap' || $uri === 'sitemap') {
    include __DIR__ . "/../private/views/pages/sitemap.php";
    exit();
}

// Include header
include __DIR__ . '/../private/views/templates/header.php';

// Check if maintenance mode is active and the client's IP is allowed
if ($site['maintenance'] && !in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
    // Include the maintenance page
    include __DIR__ . '/../private/views/pages/maintenance.php';
    exit();
}

// Include the common header
include __DIR__ . '/../private/views/templates/navbar.php';

// Use Router to dispatch the request
Router::dispatch();


