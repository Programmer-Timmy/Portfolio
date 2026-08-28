<?php
/**
 * API front controller. Included from public/index.php when the request path
 * starts with `api/`. Assumes autoload.php + config/settings.php are already
 * loaded and the session is started (so auth/SSO state is available).
 */

require_once __DIR__ . '/ApiException.php';
require_once __DIR__ . '/ApiRequest.php';
require_once __DIR__ . '/ApiResponse.php';
require_once __DIR__ . '/ApiRouter.php';
require_once __DIR__ . '/Support/Media.php';
require_once __DIR__ . '/Support/Resource.php';

foreach (glob(__DIR__ . '/Controllers/*.php') as $controller) {
    require_once $controller;
}

// The SPA is same-origin in production; in debug we also allow the Vite dev
// server so the API can be hit directly during development.
if (!empty($site['debug'])) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Vary: Origin');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = new ApiRouter();
require __DIR__ . '/routes.php';

try {
    $router->dispatch();
} catch (ApiException $e) {
    ApiResponse::sendError($e);
} catch (Throwable $e) {
    $status = 500;
    $message = !empty($site['debug'])
        ? $e->getMessage()
        : 'Something went wrong on our end.';
    $extra = !empty($site['debug'])
        ? ['type' => $e::class, 'at' => $e->getFile() . ':' . $e->getLine()]
        : [];
    ApiResponse::sendError(new ApiException($status, $message, 'internal_error', $extra));
}

exit;
