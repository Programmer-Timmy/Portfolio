<?php

/**
 * Auth endpoints for the admin SPA.
 *
 *   GET  /api/auth/csrf     issue the session CSRF token (exempt from the check)
 *   POST /api/auth/login    { username, password } -> starts an admin session
 *   POST /api/auth/logout   ends the session (CSRF-protected)
 *
 * `GET /api/auth/session` stays in MetaApi.
 */
class AuthApi
{
    private const THROTTLE_KEY = '_login_throttle';
    private const MAX_ATTEMPTS = 5;
    private const LOCK_SECONDS = 60;

    public static function csrf(): array
    {
        return ['token' => Csrf::token()];
    }

    public static function login(): ApiResponse
    {
        global $site;

        Auth::assertBrowserOrigin();

        $throttle = $_SESSION[self::THROTTLE_KEY] ?? ['count' => 0, 'until' => 0];
        if (($throttle['until'] ?? 0) > time()) {
            throw new ApiException(
                429,
                'Too many sign-in attempts. Wait a minute and try again.',
                'rate_limited',
            );
        }

        $body = ApiRequest::json();
        $username = trim((string) ($body['username'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        $errors = [];
        if ($username === '') {
            $errors['username'] = 'Enter your username.';
        }
        if ($password === '') {
            $errors['password'] = 'Enter your password.';
        }
        if ($errors) {
            throw ApiException::validation($errors);
        }

        $user = Auth::attempt($username, $password);
        if (!$user) {
            self::recordFailure($throttle);
            throw ApiException::unauthorized('Incorrect username or password.');
        }

        unset($_SESSION[self::THROTTLE_KEY]);

        // New session id on privilege change; session data is preserved.
        session_regenerate_id(true);

        $_SESSION[$site['accounts']['sessionName'] ?? 'userId'] = (int) $user->id;
        $isAdmin = (int) ($user->admin ?? 0) === 1;
        if ($isAdmin) {
            $_SESSION[$site['admin']['sessionName'] ?? 'admin'] = (int) $user->id;
        }

        $redirect = $_SESSION['redirect'] ?? null;
        unset($_SESSION['redirect']);

        return ApiResponse::ok([
            'authenticated' => true,
            'admin' => $isAdmin,
            'redirect' => is_string($redirect) && str_starts_with($redirect, '/') ? $redirect : null,
        ]);
    }

    public static function logout(): ApiResponse
    {
        global $site;

        unset(
            $_SESSION[$site['admin']['sessionName'] ?? 'admin'],
            $_SESSION[$site['accounts']['sessionName'] ?? 'userId'],
        );
        session_regenerate_id(true);

        return ApiResponse::noContent();
    }

    /** @param array{count:int,until:int} $throttle */
    private static function recordFailure(array $throttle): void
    {
        $count = (int) ($throttle['count'] ?? 0) + 1;
        if ($count >= self::MAX_ATTEMPTS) {
            $_SESSION[self::THROTTLE_KEY] = ['count' => 0, 'until' => time() + self::LOCK_SECONDS];
        } else {
            $_SESSION[self::THROTTLE_KEY] = ['count' => $count, 'until' => 0];
        }
    }
}
