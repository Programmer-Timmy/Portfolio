<?php

/** Helpers for reading query-string params and JSON request bodies. */
class ApiRequest
{
    public static function query(string $key, ?string $default = null): ?string
    {
        $value = $_GET[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }

    public static function int(string $key, int $default): int
    {
        $value = $_GET[$key] ?? null;
        return is_numeric($value) ? (int) $value : $default;
    }

    public static function bool(string $key): bool
    {
        $value = $_GET[$key] ?? null;
        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    /** Decoded JSON body as an associative array (empty array when absent/invalid). */
    public static function json(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === '' || $raw === false) {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}
