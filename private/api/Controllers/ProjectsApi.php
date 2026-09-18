<?php

class ProjectsApi
{
    public static function index(): ApiResponse
    {
        $featured = ApiRequest::bool('featured');
        $default = $featured ? 3 : 100;
        $limit = max(1, min(ApiRequest::int('limit', $default), 100));

        // loadProjects() already orders by `pinned DESC, date DESC`, so the
        // first N rows are the featured set.
        $projects = Projects::loadProjects((string) $limit);
        $projects = is_array($projects) ? $projects : [];

        $data = array_map([Resource::class, 'projectSummary'], $projects);

        return ApiResponse::collection($data, [
            'count' => count($data),
            'limit' => $limit,
            'featured' => $featured,
        ]);
    }

    public static function show(array $params): array
    {
        $id = (int) $params['id'];

        $exists = Database::get('projects', ['id'], [], ['id' => $id, 'removed' => 0]);
        if (!$exists) {
            throw ApiException::notFound("Project #$id was not found.");
        }

        return Resource::projectDetail(Projects::loadProject($id));
    }
}
