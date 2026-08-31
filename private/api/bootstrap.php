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

foreach (glob(__DIR__ . '/Support/*.php') as $support) {
    require_once $support;
}

foreach (glob(__DIR__ . '/Controllers/*.php') as $controller) {
    require_once $controller;
}
foreach (glob(__DIR__ . '/Controllers/Admin/*.php') as $controller) {
    require_once $controller;
}

// Let the browser send DELETE/PUT/PATCH as a POST + header where a host or
// proxy strips the real verb. Only honoured when the transport method is POST,
// so it can never downgrade a GET.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $override = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? '');
    if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
        $_SERVER['REQUEST_METHOD'] = $override;
    }
}

// The SPA is same-origin in production; in debug we also allow the Vite dev
// server so the API can be hit directly during development.
if (!empty($site['debug'])) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Vary: Origin');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With, X-CSRF-Token, X-HTTP-Method-Override');
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = new ApiRouter();
require __DIR__ . '/routes.php';

try {
    // Central gate for the authenticated surface. Path is derived the same way
    // ApiRouter::dispatch() does. CSRF runs before the auth check so a write
    // with a stale session gets 419 (client refetches the token and retries)
    // rather than a confusing 401/403.
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $guardPath = preg_replace('#^api/?#', '', trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/'));
    $isWrite = !in_array($method, ['GET', 'HEAD', 'OPTIONS'], true);
    $isAdminPath = $guardPath === 'admin' || str_starts_with($guardPath, 'admin/');

    if ($isWrite && ($isAdminPath || $guardPath === 'auth/logout')) {
        Csrf::verify();
    }
    if ($isAdminPath) {
        Auth::requireAdmin();
    }

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
