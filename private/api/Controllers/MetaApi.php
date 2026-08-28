<?php

class MetaApi
{
    public static function health(): array
    {
        return [
            'status' => 'ok',
            'time' => gmdate('c'),
        ];
    }

    /** GET /api/profile. Returns the full "about me" payload. */
    public static function profile(): array
    {
        $profile = require __DIR__ . '/../../config/profile.php';

        $profile['age'] = self::yearsSince($profile['birthDate'] ?? null);
        if (isset($profile['scouting']['since'])) {
            $profile['scouting']['years'] = self::yearsSince($profile['scouting']['since']);
        }

        return $profile;
    }

    /** GET /api/skills. Returns just the skills section of the profile. */
    public static function skills(): ApiResponse
    {
        $profile = require __DIR__ . '/../../config/profile.php';
        return ApiResponse::collection($profile['skills'] ?? []);
    }

    /** GET /api/auth/session. Is the current visitor signed in? */
    public static function session(): array
    {
        global $site;
        $accountKey = $site['accounts']['sessionName'] ?? 'userId';
        $adminKey = $site['admin']['sessionName'] ?? 'admin';

        return [
            'authenticated' => isset($_SESSION[$accountKey]) || isset($_SESSION[$adminKey]),
            'admin' => isset($_SESSION[$adminKey]),
        ];
    }

    private static function yearsSince(?string $date): ?int
    {
        if (!$date) {
            return null;
        }
        try {
            return (new DateTime($date))->diff(new DateTime('now'))->y;
        } catch (Exception) {
            return null;
        }
    }
}
