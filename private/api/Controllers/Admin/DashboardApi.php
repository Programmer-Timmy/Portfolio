<?php

namespace Admin;

use ApiResponse;
use Database;

/** GET /api/admin/stats — headline counts for the admin dashboard. */
class DashboardApi
{
    public static function index(): ApiResponse
    {
        $count = static fn (string $sql): int => (int) (Database::query($sql)[0]->c ?? 0);

        return ApiResponse::ok([
            'projects' => $count('SELECT COUNT(*) c FROM projects WHERE removed = 0'),
            'projectsPinned' => $count('SELECT COUNT(*) c FROM projects WHERE removed = 0 AND pinned = 1'),
            'projectsInProgress' => $count('SELECT COUNT(*) c FROM projects WHERE removed = 0 AND in_progress = 1'),
            'videos' => $count('SELECT COUNT(*) c FROM videos WHERE deleted = 0'),
            'openSourceProjects' => $count('SELECT COUNT(*) c FROM opensource_projects'),
            'pullRequests' => $count('SELECT COUNT(*) c FROM opensource_prs'),
        ]);
    }
}
