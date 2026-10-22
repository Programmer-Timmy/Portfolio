<?php

class Kuma
{
    public static function page(string $slug): array
    {
        return self::get('status-page/' . self::slug($slug));
    }

    public static function heartbeat(string $slug): array
    {
        return self::get('status-page/heartbeat/' . self::slug($slug));
    }

    private static function slug(string $slug): string
    {
        if (!preg_match('/^[\w-]{1,100}$/', $slug)) {
            throw ApiException::validation(['slug' => 'Enter a valid status page slug.']);
        }
        return $slug;
    }

    private static function get(string $path): array
    {
        $base = rtrim(Env::get('KUMA_URL', 'https://kuma.timvanderkloet.com'), '/');

        $ch = curl_init("$base/api/$path");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_TIMEOUT => 10,
        ]);
        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status === 404) {
            throw ApiException::notFound('That status page does not exist.');
        }
        $body = $raw === false ? null : json_decode($raw, true);
        if ($status !== 200 || !is_array($body)) {
            throw new ApiException(502, 'Could not reach the status server.', 'status_unavailable');
        }
        return $body;
    }
}
