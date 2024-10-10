<?php
function getPageTitle()
{
    global $titles;
    $url = strtolower($_SERVER['REQUEST_URI']); // Convert URL to lowercase for case-insensitive matching
    $pageTitle = ucfirst($titles['default']); // Default title

    // Find the corresponding title based on URL
    foreach ($titles as $urlPattern => $title) {
        if (stripos($url, $urlPattern) !== false) {
            $pageTitle = $title;
            break;
        }
    }
    return htmlspecialchars($pageTitle); // Secure and descriptive
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getPageTitle(); ?></title>
    <!-- fav icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicoins/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicoins/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicoins/favicon-16x16.png">
    <link rel="manifest" href="/img/favicoins/site.webmanifest">
    <link rel="mask-icon" href="/img/favicoins/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/img/favicoins/favicon.ico">
    <meta name="apple-mobile-web-app-title" content="Tim van der Kloet">
    <meta name="application-name" content="Tim van der Kloet">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="msapplication-config" content="/img/favicoins/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/styles.css?v=1.1" type="text/css" media="all">
    <!-- Preload favicon -->
    <link rel="preload" href="/img/favicoins/favicon-32x32.png" as="image" type="image/png" media="all">
    <!-- jQuery and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous" defer></script>
    <script src="https://kit.fontawesome.com/65416f0144.js" defer crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
