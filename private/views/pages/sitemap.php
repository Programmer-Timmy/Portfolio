<?php
header("Content-Type: application/xml; charset=utf-8");

// Define your site's base URL
$baseUrl = "https://timvanderkloet.com";

// Get all registered routes from Router
$routes = Router::getAllRoutePatterns();

// Get projects for dynamic URLs
$projects = Projects::loadProjects("10000");

// Static routes that should be in sitemap (those without parameters)
$staticRoutes = [];
$dynamicRoutes = [];

foreach ($routes as $route) {
    if (strpos($route, '{') === false) {
        // Static route (no parameters)
        // Exclude admin routes, login, 404, and maintenance from sitemap
        if ($route !== '404' && 
            $route !== 'maintenance' && 
            strpos($route, 'admin') === false &&
            $route !== 'login') {
            $staticRoutes[] = $route;
        }
    } else {
        // Dynamic route - exclude admin routes
        if (strpos($route, 'admin') === false) {
            $dynamicRoutes[] = $route;
        }
    }
}

// Get current date for lastmod
$currentDate = date('c');

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach ($staticRoutes as $route): ?>
    <url>
        <loc><?= $baseUrl ?>/<?= $route ?></loc>
        <lastmod><?= $currentDate ?></lastmod>
        <priority><?= Router::getRoutePriority($route) ?></priority>
    </url>
<?php endforeach; ?>
<?php if ($projects): ?>
    <?php foreach ($projects as $project): ?>
    <url>
        <loc><?= $baseUrl ?>/project/<?= $project->id ?></loc>
        <lastmod><?= $currentDate ?></lastmod>
        <priority>0.70</priority>
    </url>
    <?php endforeach; ?>
<?php endif; ?>
    <url>
        <loc><?= $baseUrl ?>/doc/CV.pdf</loc>
        <lastmod>2024-05-08T19:27:48+00:00</lastmod>
        <priority>0.60</priority>
    </url>
</urlset>