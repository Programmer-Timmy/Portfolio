<?php
/**
 * REST API routes. Paths are relative to `/api`.
 *
 *   GET  /api/health              service check
 *   GET  /api/profile             full "about me" payload
 *   GET  /api/skills              skills groups
 *   GET  /api/auth/session        { authenticated, admin }
 *   GET  /api/projects            ?featured=true&limit=3  -> collection
 *   GET  /api/projects/{id}       single project + gallery + contributors
 *   GET  /api/opensource          contributed repos (with PR counts)
 *   GET  /api/opensource/{id}     single repo + pull requests
 *   GET  /api/videos              ?limit=6  -> collection
 *   POST /api/contact             { name, email, message }
 *
 * @var ApiRouter $router
 */

$router->get('health', [MetaApi::class, 'health']);
$router->get('profile', [MetaApi::class, 'profile']);
$router->get('skills', [MetaApi::class, 'skills']);
$router->get('auth/session', [MetaApi::class, 'session']);

// Auth. `auth/csrf` is exempt from the CSRF check (it issues the token);
// `auth/login` is exempt too (no session yet) and guarded by a same-origin
// check + throttle instead; `auth/logout` is CSRF-protected in bootstrap.php.
$router->get('auth/csrf', [AuthApi::class, 'csrf']);
$router->post('auth/login', [AuthApi::class, 'login']);
$router->post('auth/logout', [AuthApi::class, 'logout']);

// Admin. Everything under `admin/` requires an admin session (enforced
// centrally in bootstrap.php) and CSRF on writes.
$router->get('admin/stats', [Admin\DashboardApi::class, 'index']);

$router->get('projects', [ProjectsApi::class, 'index']);
$router->get('projects/{id}', [ProjectsApi::class, 'show']);

$router->get('opensource', [OpenSourceApi::class, 'index']);
$router->get('opensource/{id}', [OpenSourceApi::class, 'show']);

$router->get('videos', [VideosApi::class, 'index']);

$router->post('contact', [ContactApi::class, 'store']);
