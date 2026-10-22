<?php

/**
 * Public status overview. Kuma is fetched server-side and only what the admin
 * allowed (and the visitor may see) leaves this class.
 *
 *   GET /api/status         slugs the visitor may see
 *   GET /api/status/{slug}  { slug, title, groups, incidents, maintenance }
 */
class StatusApi
{
    public static function index(): ApiResponse
    {
        $pages = Database::getAll('status_rules', ['slug', 'audience'], [], ['monitor_id' => 0], 'slug');
        $visible = array_filter($pages, static fn ($p) => self::canSee($p->audience));

        return ApiResponse::collection(array_values(array_map(static fn ($p) => $p->slug, $visible)));
    }

    public static function show(array $params): ApiResponse
    {
        $slug = $params['slug'];

        $allowed = [];
        foreach (Database::getAll('status_rules', ['monitor_id', 'audience'], [], ['slug' => $slug]) as $rule) {
            if (self::canSee($rule->audience)) {
                $allowed[(int) $rule->monitor_id] = true;
            }
        }
        if (!isset($allowed[0])) {
            throw ApiException::notFound('That status page does not exist.');
        }

        $page = Kuma::page($slug);
        $heartbeat = Kuma::heartbeat($slug);

        $groups = [];
        foreach ($page['publicGroupList'] ?? [] as $group) {
            $monitors = [];
            foreach ($group['monitorList'] ?? [] as $monitor) {
                $id = (int) $monitor['id'];
                if (!isset($allowed[$id])) {
                    continue;
                }
                $monitors[] = [
                    'id' => $id,
                    'name' => (string) $monitor['name'],
                    'status' => ($heartbeat['heartbeatList'][$id] ?? [])
                        ? end($heartbeat['heartbeatList'][$id])['status']
                        : null,
                    'uptime' => $heartbeat['uptimeList']["{$id}_24"] ?? null,
                ];
            }
            if ($monitors) {
                $groups[] = ['name' => (string) $group['name'], 'monitors' => $monitors];
            }
        }

        return ApiResponse::ok([
            'slug' => $slug,
            'title' => (string) ($page['config']['title'] ?? $slug),
            'groups' => $groups,
            'incidents' => array_map(static fn ($i) => [
                'id' => (int) $i['id'],
                'title' => (string) $i['title'],
                'content' => (string) $i['content'],
                'createdDate' => (string) $i['createdDate'],
            ], $page['incidents'] ?? []),
            'maintenance' => array_map(static fn ($m) => [
                'id' => (int) $m['id'],
                'title' => (string) $m['title'],
                'description' => (string) ($m['description'] ?? ''),
            ], $page['maintenanceList'] ?? []),
        ]);
    }

    private static function canSee(string $audience): bool
    {
        if ($audience === 'public' || Auth::isAdmin()) {
            return true;
        }
        $homeIps = array_filter(array_map('trim', explode(',', Env::get('HOME_IP', ''))));
        return in_array($_SERVER['REMOTE_ADDR'] ?? '', $homeIps, true);
    }
}
