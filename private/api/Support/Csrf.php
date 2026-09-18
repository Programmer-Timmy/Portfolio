<?php

/**
 * Synchronizer-token CSRF protection for the cookie-authenticated admin API.
 *
 * The token lives in the session; the SPA fetches it from `GET /api/auth/csrf`
 * and echoes it back in the `X-CSRF-Token` header on every state-changing
 * request. Enforcement is centralised in api/bootstrap.php.
 */
class Csrf
{
    private const KEY = '_csrf';

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::KEY];
    }

    /** @throws ApiException 419 when the header is missing or does not match. */
    public static function verify(): void
    {
        $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $known = $_SESSION[self::KEY] ?? '';

        if (
            !is_string($sent) || $sent === ''
            || !is_string($known) || $known === ''
            || !hash_equals($known, $sent)
        ) {
            throw new ApiException(
                419,
                'Your session has expired. Refresh the page and try again.',
                'csrf',
            );
        }
    }
}
