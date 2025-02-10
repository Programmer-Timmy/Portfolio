<?php
header("Content-Type: application/xml; charset=utf-8");

// Define your site's base URL
$baseUrl = "https://portfolio.timmygamer.nl";

$projects = Projects::loadProjects("10000")

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <url>
        <loc>https://portfolio.timmygamer.nl/</loc>
        <lastmod>2025-02-05T17:58:49+00:00</lastmod>
        <priority>1.00</priority>
    </url>
    <url>
        <loc>https://portfolio.timmygamer.nl/home</loc>
        <lastmod>2025-02-05T17:58:49+00:00</lastmod>
        <priority>0.80</priority>
    </url>
    <url>
        <loc>https://portfolio.timmygamer.nl/about</loc>
        <lastmod>2025-02-05T17:58:49+00:00</lastmod>
        <priority>0.80</priority>
    </url>
    <url>
        <loc>https://portfolio.timmygamer.nl/contact</loc>
        <lastmod>2025-02-05T17:58:49+00:00</lastmod>
        <priority>0.80</priority>
    </url>
    <url>
        <loc>https://portfolio.timmygamer.nl/projects</loc>
        <lastmod>2025-02-05T17:58:49+00:00</lastmod>
        <priority>0.80</priority>
    </url>
    <url>
        <loc>https://portfolio.timmygamer.nl/videos</loc>
        <lastmod>2025-02-05T17:58:49+00:00</lastmod>
        <priority>0.80</priority>
    </url>
    <?php if ($projects): ?>
        <?php foreach ($projects as $project): ?>
            <url>
                <loc>https://portfolio.timmygamer.nl/project/<?=$project->id?>/</loc>
                <lastmod>2025-02-05T17:58:49+00:00</lastmod>
                <priority>0.70</priority>
            </url>
        <?php endforeach;?>
    <?php endif; ?>
    <url>
        <loc>https://portfolio.timmygamer.nl/doc/CV.pdf</loc>
        <lastmod>2024-05-08T19:27:48+00:00</lastmod>
        <priority>0.60</priority>
    </url>
</urlset>