<?php
// routes.php

// Home page
Router::get('', function () {
    require_once __DIR__ . '/views/pages/home.php';
}, priority: 1.0);

Router::get('home', function () {
    require_once __DIR__ . '/views/pages/home.php';
}, priority: 1.0);

// About page
Router::get('about', function () {
    require_once __DIR__ . '/views/pages/about.php';
}, priority: 0.9);

// Contact page
Router::get('contact', function () {
    require_once __DIR__ . '/views/pages/contact.php';
}, priority: 0.9);

// Projects pages
Router::get('projects', function () {
    require_once __DIR__ . '/views/pages/projects.php';
});

Router::get('project/{id}', function ($id) {
    $_GET['id'] = $id;
    require_once __DIR__ . '/views/pages/project.php';
});

// Videos page
Router::get('videos', function () {
    require_once __DIR__ . '/views/pages/videos.php';
});

// Hello World page
Router::get('helloworld', function () {
    require_once __DIR__ . '/views/pages/helloworld.php';
}, priority: 0);

// Login page
Router::get('login', function () {
    require_once __DIR__ . '/views/pages/login.php';
}, priority: 0);

Router::post('login', function () {
    require_once __DIR__ . '/views/pages/login.php';
}, priority: 0);

// Admin pages
Router::get('admin', function () {
    require_once __DIR__ . '/views/pages/admin.php';
});

Router::get('admin/projects', function () {
    require_once __DIR__ . '/views/pages/admin/projects.php';
});

Router::post('admin/projects', function () {
    require_once __DIR__ . '/views/pages/admin/projects.php';
});

Router::get('admin/videos', function () {
    require_once __DIR__ . '/views/pages/admin/videos.php';
});

Router::get('admin/addProject', function () {
    require_once __DIR__ . '/views/pages/admin/addProject.php';
});

Router::post('admin/addProject', function () {
    require_once __DIR__ . '/views/pages/admin/addProject.php';
});

Router::get('admin/editProject/{id}', function ($id) {
    $_GET['id'] = $id;
    require_once __DIR__ . '/views/pages/admin/editProject.php';
});

Router::post('admin/editProject/{id}', function ($id) {
    $_GET['id'] = $id;
    require_once __DIR__ . '/views/pages/admin/editProject.php';
});

// Special pages
Router::get('sitemap', function () {
    // Sitemap outputs XML directly, no header/footer needed
    include __DIR__ . '/views/pages/sitemap.php';
    exit();
});