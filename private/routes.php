<?php
// routes.php

// Home page (highest priority)
Router::get('', function () {
    require_once __DIR__ . '/views/pages/home.php';
}, 1.00, [
    'title' => 'Home - Tim van der Kloet',
    'description' => 'Welcome to Tim van der Kloet\'s portfolio. Explore my projects, skills, and experience as a software developer.',
    'keywords' => 'Tim van der Kloet, Tim Kloet, Tim vd Kloet, Tim K, portfolio, software developer, web development, projects',
    'og_image' => 'https://timvanderkloet.com/img/og-home.jpg'
]);

Router::get('home', function () {
    require_once __DIR__ . '/views/pages/home.php';
}, 1.00, [
    'title' => 'Home - Tim van der Kloet',
    'description' => 'Welcome to Tim van der Kloet\'s portfolio. Explore my projects, skills, and experience as a software developer.',
    'keywords' => 'Tim van der Kloet, Tim Kloet, Tim vd Kloet, Tim K, portfolio, software developer, web development, projects',
    'og_image' => 'https://timvanderkloet.com/img/og-home.jpg'
]);

// About page
Router::get('about', function () {
    require_once __DIR__ . '/views/pages/about.php';
}, 0.90, [
    'title' => 'About Me - Tim van der Kloet',
    'description' => 'Learn more about Tim van der Kloet, his background, skills, and passion for software development.',
    'keywords' => 'about, biography, skills, experience, Tim van der Kloet, Tim Kloet, Tim vd Kloet, developer'
]);

// Contact page
Router::get('contact', function () {
    require_once __DIR__ . '/views/pages/contact.php';
}, 0.90, [
    'title' => 'Contact Me - Tim van der Kloet',
    'description' => 'Get in touch with Tim van der Kloet for collaboration opportunities, questions, or project inquiries.',
    'keywords' => 'contact, email, hire, collaboration, Tim van der Kloet, Tim Kloet, Tim vd Kloet, reach out'
]);

Router::post('contact', function () {
    require_once __DIR__ . '/views/pages/contact.php';
});

// Projects pages
Router::get('projects', function () {
    require_once __DIR__ . '/views/pages/projects.php';
}, 0.90, [
    'title' => 'Projects - Tim van der Kloet',
    'description' => 'Browse through Tim van der Kloet\'s portfolio of software development projects, including web applications and more.',
    'keywords' => 'projects, portfolio, web apps, software, development, code, Tim van der Kloet, Tim Kloet'
]);

Router::get('project/{id}', function ($id) {
    $_GET['id'] = $id;
    require_once __DIR__ . '/views/pages/project.php';
}, 0.70, [
    'title' => 'Project - Tim van der Kloet',
    'description' => 'View detailed information about this project by Tim van der Kloet.',
    'keywords' => 'project, portfolio, web development, software'
]);

Router::post('project/{id}', function ($id) {
    $_GET['id'] = $id;
    require_once __DIR__ . '/views/pages/project.php';
});

// Videos page
Router::get('videos', function () {
    require_once __DIR__ . '/views/pages/videos.php';
}, 0.85, [
    'title' => 'Videos - Tim van der Kloet',
    'description' => 'Watch videos and tutorials by Tim van der Kloet covering software development topics.',
    'keywords' => 'videos, tutorials, programming, software development'
]);

// Hello World page
Router::get('helloworld', function () {
    require_once __DIR__ . '/views/pages/helloworld.php';
}, 0.50, [
    'title' => 'Hello World - Tim van der Kloet',
    'description' => 'A simple Hello World example page.',
    'keywords' => 'hello world, example, demo',
    'robots' => 'noindex, follow'
]);

// Login page
Router::get('login', function () {
    require_once __DIR__ . '/views/pages/login.php';
}, 0.30, [
    'title' => 'Login - Tim van der Kloet',
    'description' => 'Login to access the admin panel.',
    'keywords' => 'login, admin, authentication',
    'robots' => 'noindex, follow'
]);

Router::post('login', function () {
    require_once __DIR__ . '/views/pages/login.php';
});

// Admin pages (excluded from search engines)
Router::get('admin', function () {
    require_once __DIR__ . '/views/pages/admin.php';
}, 0.20, [
    'title' => 'Admin Panel - Tim van der Kloet',
    'robots' => 'noindex, nofollow'
]);

Router::get('admin/projects', function () {
    require_once __DIR__ . '/views/pages/admin/projects.php';
}, 0.20, [
    'title' => 'Projects Admin - Tim van der Kloet',
    'robots' => 'noindex, nofollow'
]);

Router::post('admin/projects', function () {
    require_once __DIR__ . '/views/pages/admin/projects.php';
});

Router::get('admin/videos', function () {
    require_once __DIR__ . '/views/pages/admin/videos.php';
}, 0.20, [
    'title' => 'Videos Admin - Tim van der Kloet',
    'robots' => 'noindex, nofollow'
]);

Router::post('admin/videos', function () {
    require_once __DIR__ . '/views/pages/admin/videos.php';
});

Router::get('admin/addProject', function () {
    require_once __DIR__ . '/views/pages/admin/addProject.php';
}, 0.20, [
    'title' => 'Add Project - Tim van der Kloet',
    'robots' => 'noindex, nofollow'
]);

Router::post('admin/addProject', function () {
    require_once __DIR__ . '/views/pages/admin/addProject.php';
});

Router::get('admin/editProject/{id}', function ($id) {
    $_GET['id'] = $id;
    require_once __DIR__ . '/views/pages/admin/editProject.php';
}, 0.20, [
    'title' => 'Edit Project - Tim van der Kloet',
    'robots' => 'noindex, nofollow'
]);

Router::post('admin/editProject/{id}', function ($id) {
    $_GET['id'] = $id;
    require_once __DIR__ . '/views/pages/admin/editProject.php';
});

// Special pages
Router::get('sitemap.xml', function () {
    // Sitemap outputs XML directly, no header/footer needed
    include __DIR__ . '/views/pages/sitemap.php';
}, 0.50, [
    'title' => 'Sitemap - Tim van der Kloet',
    'robots' => 'noindex, follow'
], true);

Router::get('maintenance', function () {
    require_once __DIR__ . '/views/pages/maintenance.php';
}, 0.10, [
    'title' => 'Under Maintenance - Tim van der Kloet',
    'robots' => 'noindex, nofollow'
]);

Router::get('404', function () {
    http_response_code(404);
    require_once __DIR__ . '/views/pages/404.php';
}, 0.10, [
    'title' => '404 - Page Not Found - Tim van der Kloet',
    'description' => 'The page you are looking for could not be found.',
    'robots' => 'noindex, nofollow'
]);
