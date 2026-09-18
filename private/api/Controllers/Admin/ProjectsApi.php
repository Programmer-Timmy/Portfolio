<?php

namespace Admin;

use ApiException;
use ApiRequest;
use ApiResponse;
use Database;
use Projects;
use Resource;

/**
 * Admin projects API.
 *
 *   GET    /api/admin/projects              ?includeRemoved=1
 *   GET    /api/admin/projects/{id}         edit-form payload
 *   POST   /api/admin/projects              multipart create
 *   POST   /api/admin/projects/{id}         multipart update
 *   DELETE /api/admin/projects/{id}         soft delete
 *   POST   /api/admin/projects/{id}/restore
 *
 * Create / update take a `multipart/form-data` body:
 *   payload      JSON { name, link, github, pinned, inProgress, privateRepo,
 *                       description: DeltaOp[]|null, languages: [...], contributors: [...] }
 *   imageState   JSON { images: string[], removed: string[] }   (update only)
 *   images[]     the new image files
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
        return Resource::projectEditable(self::find((int) $params['id']));
    }

    public static function store(): ApiResponse
    {
        $payload = self::payload();
        $files = ApiRequest::files('images');

        if (count($files) < 1) {
            throw ApiException::validation(['images' => 'Add at least one image.']);
        }

        $id = Projects::addProject(
            $payload['name'],
            $payload['description'],
            $payload['link'],
            $payload['github'],
            $files,
            $payload['pinned'],
            $payload['inProgress'],
            $payload['privateRepo'],
        );

        if (!is_numeric($id)) {
            throw new ApiException(422, (string) $id, 'operation_failed');
        }
        $id = (int) $id;

        self::saveLanguages($id, $payload['languages'], true);
        self::saveContributors($id, $payload['contributors'], true);

        return ApiResponse::created(Resource::projectEditable(Projects::loadProject($id)));
    }

    public static function update(array $params): ApiResponse
    {
        $id = (int) $params['id'];
        self::assertExists($id);

        $payload = self::payload();
        $files = ApiRequest::files('images');
        $imageState = is_string($_POST['imageState'] ?? null) ? $_POST['imageState'] : null;

        $error = Projects::updateProject(
            $payload['name'],
            $payload['description'],
            $payload['link'],
            $payload['github'],
            $files,
            $payload['pinned'],
            $payload['inProgress'],
            $id,
            $payload['privateRepo'],
            $imageState,
        );
        if (is_string($error) && $error !== '') {
            throw new ApiException(422, $error, 'operation_failed');
        }

        self::saveLanguages($id, $payload['languages'], false);
        self::saveContributors($id, $payload['contributors'], false);

        return ApiResponse::ok(Resource::projectEditable(Projects::loadProject($id)));
    }

    public static function destroy(array $params): ApiResponse
    {
        $id = (int) $params['id'];
        self::assertExists($id);

        // `?hard=1` permanently removes the row, its child records and image
        // files; the default is a reversible soft delete.
        $error = ApiRequest::bool('hard')
            ? Projects::purgeProject($id)
            : Projects::deleteProject($id);

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

    // --- helpers ---------------------------------------------------------------

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

    /**
     * @return array{name:string, link:string, github:string, description:string,
     *   pinned:int, inProgress:int, privateRepo:?int, languages:array, contributors:array}
     */
    private static function payload(): array
    {
        $raw = $_POST['payload'] ?? null;
        $data = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($data)) {
            throw ApiException::validation(['payload' => 'The form data was malformed.']);
        }

        $name = trim((string) ($data['name'] ?? ''));
        $link = trim((string) ($data['link'] ?? ''));
        $github = trim((string) ($data['github'] ?? ''));

        $errors = [];
        if ($name === '') {
            $errors['name'] = 'Enter a title.';
        } elseif (mb_strlen($name) > 20) {
            $errors['name'] = 'Keep the title to 20 characters or fewer.';
        }
        if ($link !== '' && !filter_var($link, FILTER_VALIDATE_URL)) {
            $errors['link'] = 'Enter a valid URL (including https://).';
        }
        if ($github !== '' && !preg_match('~^https?://(www\.)?github\.com/[\w.-]+/[\w.-]+/?$~i', $github)) {
            $errors['github'] = 'Enter a GitHub repository URL.';
        }

        $description = $data['description'] ?? null;
        if (self::deltaIsEmpty($description)) {
            $errors['description'] = 'Add a description.';
        }

        if ($errors) {
            throw ApiException::validation($errors);
        }

        return [
            'name' => $name,
            'link' => $link,
            'github' => $github,
            'description' => json_encode(is_array($description) ? $description : []),
            'pinned' => !empty($data['pinned']) ? 1 : 0,
            'inProgress' => !empty($data['inProgress']) ? 1 : 0,
            'privateRepo' => array_key_exists('privateRepo', $data) && $data['privateRepo'] !== null
                ? (!empty($data['privateRepo']) ? 1 : 0)
                : null,
            'languages' => is_array($data['languages'] ?? null) ? $data['languages'] : [],
            'contributors' => is_array($data['contributors'] ?? null) ? $data['contributors'] : [],
        ];
    }

    private static function deltaIsEmpty(mixed $ops): bool
    {
        if (!is_array($ops)) {
            return true;
        }
        $text = '';
        foreach ($ops as $op) {
            if (is_array($op) && isset($op['insert']) && is_string($op['insert'])) {
                $text .= $op['insert'];
            }
        }
        return trim($text) === '';
    }

    private static function saveLanguages(int $id, array $languages, bool $isNew): void
    {
        $shaped = [];
        foreach ($languages as $l) {
            if (empty($l['programmingLanguageId'])) {
                continue;
            }
            $shaped[] = [
                'programming_languages_id' => (int) $l['programmingLanguageId'],
                'percentage' => isset($l['percentage']) && $l['percentage'] !== null ? (float) $l['percentage'] : null,
            ];
        }

        $json = json_encode($shaped);
        $error = $isNew
            ? Projects::addProjectLanguages($json, $id)
            : Projects::updateProjectLanguages($json, $id);

        if (is_string($error) && $error !== '') {
            throw new ApiException(422, $error, 'operation_failed');
        }
    }

    private static function saveContributors(int $id, array $contributors, bool $isNew): void
    {
        $shaped = [];
        foreach ($contributors as $c) {
            if (empty($c['id'])) {
                continue;
            }
            $shaped[] = [
                'user' => [
                    'id' => (int) $c['id'],
                    'login' => $c['login'] ?? null,
                    'avatar_url' => $c['avatarUrl'] ?? null,
                    'html_url' => $c['profileUrl'] ?? null,
                ],
                'contributions' => (int) ($c['contributions'] ?? 0),
            ];
        }

        $json = json_encode($shaped);
        $error = $isNew
            ? Projects::addProjectContributors($json, $id)
            : Projects::updateProjectContributors($json, $id);

        if (is_string($error) && $error !== '') {
            throw new ApiException(422, $error, 'operation_failed');
        }
    }
}
