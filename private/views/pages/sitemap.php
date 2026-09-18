<?php
header("Content-Type: application/xml; charset=utf-8");

// Define your site's base URL
$baseUrl = "https://timvanderkloet.com";

// Get all registered routes from Router
$routes = Router::getAllRoutePatterns();

// Get dynamic content for per-item URLs
$projects = Projects::loadProjects("10000");
$openSourceProjects = OpenSource::getAll();

// Static routes that should be in sitemap (those without parameters).
// A route is skipped when it's an admin route, or its own SEO config marks
// it noindex (login, 404, maintenance, helloworld, sitemap.xml itself, ...).
// 'home' is dropped as a duplicate of the '' (root) route.
$staticRoutes = [];

foreach ($routes as $route) {
    if (strpos($route, '{') !== false) {
        continue;
    }
    if ($route === 'home' || strpos($route, 'admin') !== false) {
        continue;
    }
    if (strpos(Router::getRouteRobots($route), 'noindex') !== false) {
        continue;
    }
    $staticRoutes[] = $route;
}

// Get current date for lastmod
$currentDate = date('c');

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
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
<?php if ($openSourceProjects): ?>
    <?php foreach ($openSourceProjects as $project): ?>
    <url>
        <loc><?= $baseUrl ?>/opensource/<?= $project->id ?></loc>
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
