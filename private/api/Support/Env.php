<?php

/**
 * Reads the repo-root `.env` (INI format, gitignored) once and memoizes it.
 * Single choke point for secrets so a token never has to be echoed into a
 * template again. Keys in use: GITHUB_TOKEN, API_KEY (YouTube), CHANNEL_ID.
 */
class Env
{
    /** @var array<string, string>|null */
    private static ?array $values = null;

    public static function get(string $key, ?string $default = null): ?string
    {
        if (self::$values === null) {
            $path = dirname(__DIR__, 3) . '/.env';
            self::$values = is_file($path) ? (parse_ini_file($path) ?: []) : [];
        }

        $value = self::$values[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }
}
