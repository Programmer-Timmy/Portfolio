<?php

namespace Admin;

use ApiException;
use ApiRequest;
use ApiResponse;
use Database;
use GitHub;
use OpenSource;
use Resource;

/**
 * Admin open-source API.
 *
 *   GET    /api/admin/opensource        repos + PR counts
 *   POST   /api/admin/opensource        { repoUrl, username } — import PRs
 *   DELETE /api/admin/opensource/{id}   remove repo + its PRs
 */
class OpenSourceApi
{
    public static function index(): ApiResponse
    {
        $projects = OpenSource::getAll();
        $projects = is_array($projects) ? $projects : [];

        $data = array_map([Resource::class, 'openSourceProject'], $projects);

        return ApiResponse::collection($data, [
            'count' => count($data),
            'pullRequestTotal' => array_sum(array_map(
                static fn ($p) => $p['pullRequestCount'] ?? 0,
                $data,
            )),
        ]);
    }

    public static function store(): ApiResponse
    {
        $body = ApiRequest::json();
        $repoUrl = trim((string) ($body['repoUrl'] ?? ''));
        $username = trim((string) ($body['username'] ?? ''));

        $errors = [];
        if (!GitHub::parseRepo($repoUrl)) {
            $errors['repoUrl'] = 'Enter a GitHub repository URL.';
        }
        if ($username === '' || !preg_match('#^[\w-]{1,39}$#', $username)) {
            $errors['username'] = 'Enter a valid GitHub username.';
        }
        if ($errors) {
            throw ApiException::validation($errors);
        }

        $error = OpenSource::addProject($repoUrl, $username);
        if ($error !== '') {
            throw new ApiException(422, $error, 'operation_failed');
        }

        $parsed = GitHub::parseRepo($repoUrl);
        $name = $parsed['owner'] . '/' . $parsed['repo'];
        $row = Database::get('opensource_projects', ['*'], [], ['name' => $name]);
        $row->pr_count = count(
            Database::getAll('opensource_prs', ['id'], [], ['project_id' => $row->id]),
        );

        return ApiResponse::created(Resource::openSourceProject($row));
    }

    public static function destroy(array $params): ApiResponse
    {
        $id = (int) $params['id'];
        if (!Database::get('opensource_projects', ['id'], [], ['id' => $id])) {
            throw ApiException::notFound("Open-source project #$id was not found.");
        }

        $error = OpenSource::deleteProject($id);
        if ($error !== '') {
            throw new ApiException(422, $error, 'operation_failed');
        }

        return ApiResponse::noContent();
    }
}
