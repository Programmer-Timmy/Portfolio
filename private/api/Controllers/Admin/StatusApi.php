<?php

namespace Admin;

use ApiException;
use ApiRequest;
use ApiResponse;
use Database;
use Kuma;

/**
 * Admin side of the status overview: which Kuma pages and monitors are shown,
 * and to whom. No rule means hidden.
 *
 *   GET /api/admin/status         configured slugs
 *   GET /api/admin/status/{slug}  every monitor on the Kuma page with its audience
 *   PUT /api/admin/status/{slug}  { page, monitors: { id: audience } }
 */
class StatusApi
{
    private const AUDIENCES = ['hidden', 'private', 'public'];

    public static function index(): ApiResponse
    {
        $pages = Database::getAll('status_rules', ['slug'], [], ['monitor_id' => 0], 'slug');

        return ApiResponse::collection(array_map(static fn ($p) => $p->slug, $pages));
    }

    public static function show(array $params): ApiResponse
    {
        $slug = $params['slug'];
        $page = Kuma::page($slug);
        $rules = self::rules($slug);

        $groups = [];
        foreach ($page['publicGroupList'] ?? [] as $group) {
            $groups[] = [
                'name' => (string) $group['name'],
                'monitors' => array_map(static fn ($m) => [
                    'id' => (int) $m['id'],
                    'name' => (string) $m['name'],
                    'audience' => $rules[(int) $m['id']] ?? 'hidden',
                ], $group['monitorList'] ?? []),
            ];
        }

        return ApiResponse::ok([
            'slug' => $slug,
            'title' => (string) ($page['config']['title'] ?? $slug),
            'audience' => $rules[0] ?? 'hidden',
            'groups' => $groups,
        ]);
    }

    public static function update(array $params): ApiResponse
    {
        $slug = $params['slug'];
        Kuma::page($slug);

        $body = ApiRequest::json();
        $audiences = [0 => $body['page'] ?? 'hidden'];
        foreach ($body['monitors'] ?? [] as $id => $audience) {
            $audiences[(int) $id] = $audience;
        }
        foreach ($audiences as $audience) {
            if (!in_array($audience, self::AUDIENCES, true)) {
                throw ApiException::validation(['audience' => 'Choose hidden, private or public.']);
            }
        }

        $db = Database::beginTransaction();
        Database::delete('status_rules', ['slug' => $slug], $db);
        if ($audiences[0] !== 'hidden') {
            foreach ($audiences as $id => $audience) {
                if ($audience !== 'hidden') {
                    Database::insert('status_rules', ['slug', 'monitor_id', 'audience'], [$slug, $id, $audience], $db);
                }
            }
        }
        Database::commit($db);

        return self::show($params);
    }

    /** @return array<int, string> monitor id (0 = the page itself) => audience */
    private static function rules(string $slug): array
    {
        $rules = [];
        foreach (Database::getAll('status_rules', ['monitor_id', 'audience'], [], ['slug' => $slug]) as $rule) {
            $rules[(int) $rule->monitor_id] = $rule->audience;
        }
        return $rules;
    }
}
