<?php

namespace Admin;

use ApiException;
use ApiRequest;
use ApiResponse;
use GitHub;

/**
 * Admin-only proxy for the GitHub calls the project form needs. The token
 * stays server-side (Support/GitHub + Support/Env).
 *
 *   GET /api/admin/github/repo?url=          { exists, private, description, ... }
 *   GET /api/admin/github/languages?url=     { languages: [...], unmapped: [...] }
 *   GET /api/admin/github/contributors?url=  [ { id, login, avatarUrl, ... } ]
 *   GET /api/admin/github/user?login=        { id, login, avatarUrl, profileUrl }
 */
class GitHubApi
{
    public static function repo(): ApiResponse
    {
        return ApiResponse::ok(GitHub::repo(self::url()));
    }

    public static function languages(): ApiResponse
    {
        return ApiResponse::ok(GitHub::languages(self::url()));
    }

    public static function contributors(): ApiResponse
    {
        return ApiResponse::collection(GitHub::contributors(self::url()));
    }

    public static function user(): ApiResponse
    {
        return ApiResponse::ok(GitHub::user((string) ApiRequest::query('login', '')));
    }

    private static function url(): string
    {
        $url = (string) ApiRequest::query('url', '');
        if ($url === '') {
            throw ApiException::validation(['url' => 'Provide a GitHub repository URL.']);
        }
        return $url;
    }
}
