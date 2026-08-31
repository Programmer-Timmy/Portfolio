<?php

namespace Admin;

use ApiException;
use ApiRequest;
use ApiResponse;
use Database;
use Projects;
use Resource;

/**
 * Admin projects API. Reads use `Projects::loadAllProjects()` (no `removed`
 * filter); deletes are soft (`removed = 1`) and reversible via `restore`.
 * Create / update land in a later milestone.
 */
class ProjectsApi
{
    public static function index(): ApiResponse
    {
        $includeRemoved = ApiRequest::bool('includeRemoved');

        $rows = Projects::loadAllProjects();
        if (!$includeRemoved) {
            $rows = array_filter($rows, static fn ($p) => !($p->removed ?? false));
        }

        $data = array_map([Resource::class, 'projectAdminRow'], array_values($rows));

        return ApiResponse::collection($data, ['count' => count($data)]);
    }

    public static function show(array $params): array
    {
        $project = self::find((int) $params['id']);
        return Resource::projectEditable($project);
    }

    public static function destroy(array $params): ApiResponse
    {
        $id = (int) $params['id'];
        self::assertExists($id);

        $error = Projects::deleteProject($id);
        if ($error !== '') {
            throw new ApiException(422, $error, 'operation_failed');
        }

        return ApiResponse::noContent();
    }

    public static function restore(array $params): ApiResponse
    {
        $id = (int) $params['id'];
        self::assertExists($id);

        $error = Projects::restoreProject($id);
        if ($error !== '') {
            throw new ApiException(422, $error, 'operation_failed');
        }

        return ApiResponse::noContent();
    }

    private static function assertExists(int $id): void
    {
        if (!Database::get('projects', ['id'], [], ['id' => $id])) {
            throw ApiException::notFound("Project #$id was not found.");
        }
    }

    private static function find(int $id): object
    {
        self::assertExists($id);
        $project = Projects::loadProject($id); // filters removed = 0
        if (!$project) {
            throw ApiException::notFound("Project #$id is not editable (it may be deleted).");
        }
        return $project;
    }
}
