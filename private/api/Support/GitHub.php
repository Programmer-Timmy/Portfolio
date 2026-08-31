<?php

/**
 * Server-side GitHub REST client for the admin. Keeps GITHUB_TOKEN out of the
 * browser (the old admin embedded it in page JS). All methods take a repo URL
 * or username and throw ApiException on any non-recoverable failure.
 */
class GitHub
{
    private const API = 'https://api.github.com';

    /** @return array{owner:string, repo:string}|null */
    public static function parseRepo(string $url): ?array
    {
        $url = preg_replace('/[?#].*$/', '', trim($url));
        if (!preg_match('~^https?://(?:www\.)?github\.com/([\w.-]+)/([\w.-]+?)(?:\.git)?/?$~i', $url, $m)) {
            return null;
        }
        return ['owner' => $m[1], 'repo' => $m[2]];
    }

    /** @return array{exists:bool, fullName?:string, private?:bool, description?:?string, htmlUrl?:?string, defaultBranch?:?string} */
    public static function repo(string $url): array
    {
        $r = self::mustParse($url);
        [$status, $body] = self::request("/repos/{$r['owner']}/{$r['repo']}");

        if ($status === 404) {
            return ['exists' => false];
        }
        self::assertOk($status);

        return [
            'exists' => true,
            'fullName' => $body['full_name'] ?? "{$r['owner']}/{$r['repo']}",
            'private' => (bool) ($body['private'] ?? false),
            'description' => $body['description'] ?? null,
            'htmlUrl' => $body['html_url'] ?? null,
            'defaultBranch' => $body['default_branch'] ?? null,
        ];
    }

    /**
     * Language breakdown mapped onto the programming_languages catalogue.
     * Unknown names are collected in `unmapped` (and their bytes folded into
     * "Other"); anything under 1% is folded into "Other" too.
     *
     * @return array{languages:list<array{programmingLanguageId:int,name:string,color:?string,percentage:float}>, unmapped:list<string>}
     */
    public static function languages(string $url): array
    {
        $r = self::mustParse($url);
        [$status, $body] = self::request("/repos/{$r['owner']}/{$r['repo']}/languages");

        if ($status === 404) {
            return ['languages' => [], 'unmapped' => []];
        }
        self::assertOk($status);

        $body = is_array($body) ? $body : [];
        $total = array_sum($body);
        if ($total <= 0) {
            return ['languages' => [], 'unmapped' => []];
        }

        $catalogue = [];
        foreach (Database::getAll('programming_languages', ['id', 'name', 'color']) as $row) {
            $catalogue[mb_strtolower($row->name)] = $row;
        }

        $mapped = [];
        $unmapped = [];
        $otherBytes = 0;

        foreach ($body as $name => $bytes) {
            $row = $catalogue[mb_strtolower((string) $name)] ?? null;
            if (!$row) {
                $unmapped[] = (string) $name;
                $otherBytes += $bytes;
                continue;
            }
            $pct = round($bytes / $total * 100, 1);
            if ($pct < 1) {
                $otherBytes += $bytes;
                continue;
            }
            $mapped[] = [
                'programmingLanguageId' => (int) $row->id,
                'name' => $row->name,
                'color' => $row->color ?? null,
                'percentage' => $pct,
            ];
        }

        if ($otherBytes > 0 && isset($catalogue['other'])) {
            $other = $catalogue['other'];
            $mapped[] = [
                'programmingLanguageId' => (int) $other->id,
                'name' => $other->name,
                'color' => $other->color ?? null,
                'percentage' => round($otherBytes / $total * 100, 1),
            ];
        }

        return ['languages' => $mapped, 'unmapped' => $unmapped];
    }

    /** @return list<array{id:int,login:?string,avatarUrl:?string,profileUrl:?string,contributions:int}> */
    public static function contributors(string $url): array
    {
        $r = self::mustParse($url);
        [$status, $body] = self::request("/repos/{$r['owner']}/{$r['repo']}/contributors?per_page=100");

        if ($status === 404 || $status === 204) {
            return [];
        }
        self::assertOk($status);
        if (!is_array($body)) {
            return [];
        }

        $out = [];
        foreach ($body as $c) {
            $login = $c['login'] ?? '';
            if (($c['type'] ?? '') === 'Bot' || str_contains($login, '[bot]')) {
                continue;
            }
            $out[] = [
                'id' => (int) ($c['id'] ?? 0),
                'login' => $login !== '' ? $login : null,
                'avatarUrl' => $c['avatar_url'] ?? null,
                'profileUrl' => $c['html_url'] ?? null,
                'contributions' => (int) ($c['contributions'] ?? 0),
            ];
        }
        return $out;
    }

    /** @return array{id:int,login:string,avatarUrl:?string,profileUrl:?string} */
    public static function user(string $login): array
    {
        $login = trim($login);
        if ($login === '' || !preg_match('#^[\w-]{1,39}$#', $login)) {
            throw ApiException::validation(['login' => 'Enter a valid GitHub username.']);
        }

        [$status, $body] = self::request('/users/' . rawurlencode($login));
        if ($status === 404) {
            throw ApiException::notFound("There is no GitHub user @$login.");
        }
        self::assertOk($status);

        return [
            'id' => (int) ($body['id'] ?? 0),
            'login' => $body['login'] ?? $login,
            'avatarUrl' => $body['avatar_url'] ?? null,
            'profileUrl' => $body['html_url'] ?? null,
        ];
    }

    /** @return array{owner:string, repo:string} */
    private static function mustParse(string $url): array
    {
        $r = self::parseRepo($url);
        if (!$r) {
            throw ApiException::validation(['url' => "That doesn't look like a GitHub repository URL."]);
        }
        return $r;
    }

    /** @return array{0:int, 1:mixed} */
    private static function request(string $path): array
    {
        $token = Env::get('GITHUB_TOKEN');
        [$status, $body] = self::curlGet($path, $token);

        // A configured-but-rejected token (expired PAT) shouldn't break lookups
        // for public repos — retry unauthenticated (lower rate limit).
        if ($status === 401 && $token) {
            [$status, $body] = self::curlGet($path, null);
        }

        return [$status, $body];
    }

    /** @return array{0:int, 1:mixed} */
    private static function curlGet(string $path, ?string $token): array
    {
        $headers = [
            'Accept: application/vnd.github+json',
            'User-Agent: portfolio-admin',
            'X-GitHub-Api-Version: 2022-11-28',
        ];
        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init(self::API . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 10,
        ]);
        $raw = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new ApiException(502, 'Could not reach GitHub: ' . $error, 'github_unavailable');
        }
        return [$status, json_decode($raw, true)];
    }

    private static function assertOk(int $status): void
    {
        if ($status === 403 || $status === 429) {
            throw new ApiException(502, 'GitHub rate limit reached. Try again in a few minutes.', 'github_unavailable');
        }
        if ($status === 401) {
            throw new ApiException(502, 'GitHub rejected the API token.', 'github_unavailable');
        }
        if ($status < 200 || $status >= 300) {
            throw new ApiException(502, "GitHub returned an unexpected response ($status).", 'github_unavailable');
        }
    }
}
