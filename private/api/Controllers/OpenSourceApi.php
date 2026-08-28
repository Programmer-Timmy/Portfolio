<?php

class OpenSourceApi
{
    public static function index(): ApiResponse
    {
        $projects = OpenSource::getAll();
        $projects = is_array($projects) ? $projects : [];

        $data = array_map([Resource::class, 'openSourceProject'], $projects);

        return ApiResponse::collection($data, [
            'count' => count($data),
            'pullRequestTotal' => array_sum(array_column($data, 'pullRequestCount')),
        ]);
    }

    public static function show(array $params): array
    {
        $id = (int) $params['id'];
        $project = OpenSource::getProject($id);

        if (!$project) {
            throw ApiException::notFound("Open-source project #$id was not found.");
        }

        $prs = is_array($project->prs ?? null) ? $project->prs : [];

        return array_merge(Resource::openSourceProject($project), [
            'pullRequestCount' => count($prs),
            'pullRequests' => array_map([Resource::class, 'pullRequest'], $prs),
        ]);
    }
}
