<?php

/**
 * Session-based auth for the API. Reads the same session keys the legacy PHP
 * site uses (`$site['accounts']['sessionName']` = a logged-in user id,
 * `$site['admin']['sessionName']` = an admin user id), so a session started on
 * either surface is honoured by the other.
 */
class Auth
{
    public static function userId(): ?int
    {
        global $site;
        $key = $site['accounts']['sessionName'] ?? 'userId';
        return isset($_SESSION[$key]) ? (int) $_SESSION[$key] : null;
    }

    public static function isAdmin(): bool
    {
        global $site;
        $key = $site['admin']['sessionName'] ?? 'admin';
        return isset($_SESSION[$key]);
    }

    /**
     * @throws ApiException 401 when not signed in, 403 when signed in without admin.
     * @return int the admin user id
     */
    public static function requireAdmin(): int
    {
        global $site;
        if (!self::isAdmin()) {
            throw self::userId() === null
                ? ApiException::unauthorized()
                : ApiException::forbidden();
        }
        return (int) $_SESSION[$site['admin']['sessionName'] ?? 'admin'];
    }

    /** Verify credentials against the users table. Returns the row or null. */
    public static function attempt(string $username, string $password): ?object
    {
        global $site;
        $table = $site['user-adminTable'] ?? 'users';

        $user = Database::get($table, ['*'], [], ['username' => $username]);
        if (!$user || empty($user->password_hash) || !is_string($user->password_hash)) {
            return null;
        }
        if (!password_verify($password, $user->password_hash)) {
            return null;
        }
        return $user;
    }

    /**
     * Cheap same-origin gate for the (necessarily CSRF-exempt) login endpoint.
     * Modern browsers send `Sec-Fetch-Site`; a cross-site value means the
     * request was triggered by another origin. Absent header -> allow (the rest
     * of the admin surface is token-protected anyway).
     *
     * @throws ApiException 403 on a cross-site request.
     */
    public static function assertBrowserOrigin(): void
    {
        $site = $_SERVER['HTTP_SEC_FETCH_SITE'] ?? null;
        if ($site !== null && !in_array($site, ['same-origin', 'same-site', 'none'], true)) {
            throw ApiException::forbidden('Request blocked (cross-site).');
        }
    }
}
