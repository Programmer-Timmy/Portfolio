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

    public static function isMultipart(): bool
    {
        return str_starts_with(strtolower($_SERVER['CONTENT_TYPE'] ?? ''), 'multipart/form-data');
    }

    /**
     * The request body as an assoc array: `$_POST` for a multipart request,
     * otherwise the decoded JSON body.
     */
    public static function input(): array
    {
        return (self::isMultipart() || !empty($_POST)) ? $_POST : self::json();
    }

    /**
     * Uploaded files as a flat list of `$_FILES`-style entries. Accepts both
     * `name="images[]"` (PHP nests these) and `name="image-0", "image-1", ...`.
     * Entries with UPLOAD_ERR_NO_FILE are skipped.
     *
     * @return list<array{name:string, type:string, tmp_name:string, error:int, size:int}>
     */
    public static function files(string $prefix = 'images'): array
    {
        $out = [];

        // images[] -> $_FILES[$prefix] with array values per key
        if (isset($_FILES[$prefix]) && is_array($_FILES[$prefix]['name'] ?? null)) {
            $group = $_FILES[$prefix];
            foreach (array_keys($group['name']) as $i) {
                if (($group['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $out[] = [
                    'name' => $group['name'][$i],
                    'type' => $group['type'][$i] ?? '',
                    'tmp_name' => $group['tmp_name'][$i] ?? '',
                    'error' => $group['error'][$i] ?? UPLOAD_ERR_OK,
                    'size' => $group['size'][$i] ?? 0,
                ];
            }
        }

        // image-0, image-1, ... (or any single-file entry that isn't the array form)
        foreach ($_FILES as $key => $file) {
            if ($key === $prefix || is_array($file['name'] ?? null)) {
                continue;
            }
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $out[] = $file;
        }

        return $out;
    }
}
