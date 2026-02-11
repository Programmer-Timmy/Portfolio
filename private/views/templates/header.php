<?php
function getPageTitle()
{
    return htmlspecialchars(Router::getPageTitle());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getPageTitle(); ?></title>
    
    <!-- SEO Meta Tags -->
    <?php if (Router::getPageDescription()): ?>
    <meta name="description" content="<?php echo htmlspecialchars(Router::getPageDescription()); ?>">
    <?php endif; ?>
    
    <?php if (Router::getPageKeywords()): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars(Router::getPageKeywords()); ?>">
    <?php endif; ?>
    
    <meta name="robots" content="<?php echo htmlspecialchars(Router::getRobots()); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars(Router::getPageTitle()); ?>">
    <?php if (Router::getPageDescription()): ?>
    <meta property="og:description" content="<?php echo htmlspecialchars(Router::getPageDescription()); ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars('https://timvanderkloet.com' . $_SERVER['REQUEST_URI']); ?>">
    <?php if (Router::getOGImage()): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars(Router::getOGImage()); ?>">
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars(Router::getPageTitle()); ?>">
    <?php if (Router::getPageDescription()): ?>
    <meta name="twitter:description" content="<?php echo htmlspecialchars(Router::getPageDescription()); ?>">
    <?php endif; ?>
    <?php if (Router::getOGImage()): ?>
    <meta name="twitter:image" content="<?php echo htmlspecialchars(Router::getOGImage()); ?>">
    <?php endif; ?>
    
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
    <link rel="stylesheet" href="/css/styles.css?v=1.8.2" type="text/css" media="all">
    <!-- Preload favicon -->
    <link rel="preload" href="/img/favicoins/favicon-32x32.png" as="image" type="image/png" media="all">
    
    <!-- Preload critical images based on page -->
    <?php
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if ($uri === '' || $uri === 'home' || $uri === 'about' || $uri === 'contact') {
        // Preload profile photo on pages where it's above the fold
        echo '<link rel="preload" href="/img/profielfoto.JPG" as="image" media="all">';
    }
    ?>
    
    <!-- jQuery and Font Awesome -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/65416f0144.js" defer crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
