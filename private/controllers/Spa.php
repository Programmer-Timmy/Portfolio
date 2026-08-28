<?php

/**
 * Serves the built React app (frontend/ -> public/app) for routes that have
 * been migrated. PHP remains the front controller: session, SSO, maintenance
 * and auth checks in index.php run first, so an unauthenticated visitor gets a
 * server-side redirect before the SPA shell is ever sent.
 *
 * Configure which routes the SPA owns in $site['spa']['routes'].
 */
class Spa
{
    private const SHELL = __DIR__ . '/../../public/app/index.html';

    /** Is this URI handled by the React app? */
    public static function handles(string $uri): bool
    {
        global $site;

        if (empty($site['spa']['enabled'])) {
            return false;
        }

        foreach ($site['spa']['routes'] ?? [] as $route) {
            $regex = preg_replace('#\{[a-zA-Z_]+\}#', '[a-zA-Z0-9_-]+', trim($route, '/'));
            if (preg_match('#^' . $regex . '$#', $uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Output the SPA shell. Returns false when the build is missing so the
     * caller can fall back to the existing PHP view.
     */
    public static function render(): bool
    {
        if (!is_file(self::SHELL)) {
            return false;
        }

        http_response_code(200);
        header('Content-Type: text/html; charset=utf-8');
        // Shell is tiny and revalidated often during the migration.
        header('Cache-Control: no-cache');
        readfile(self::SHELL);

        return true;
    }

    public static function isBuilt(): bool
    {
        return is_file(self::SHELL);
    }
}
