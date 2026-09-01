<?php

namespace Admin;

use ApiException;
use ApiRequest;
use ApiResponse;
use Database;
use Resource;
use Videos;

/**
 * Admin videos API. Videos are imported from YouTube by `sync`; the admin can
 * pin, rename, or hide (soft delete) them.
 *
 *   GET    /api/admin/videos            ?includeDeleted=1
 *   POST   /api/admin/videos/sync       pull latest from the channel
 *   POST   /api/admin/videos/{id}/pin   toggle pinned
 *   PATCH  /api/admin/videos/{id}       { title }
 *   DELETE /api/admin/videos/{id}       soft delete
 */
class VideosApi
{
    public static function index(): ApiResponse
    {
        $includeDeleted = ApiRequest::bool('includeDeleted');

        $videos = Videos::getAll();
        $videos = is_array($videos) ? $videos : [];
        if (!$includeDeleted) {
            $videos = array_filter($videos, static fn ($v) => !($v->deleted ?? false));
        }

        $data = array_map([Resource::class, 'video'], array_values($videos));

        return ApiResponse::collection($data, ['count' => count($data)]);
    }

    public static function sync(): ApiResponse
    {
        try {
            $summary = Videos::add();
        } catch (\Throwable $e) {
            throw new ApiException(502, $e->getMessage(), 'youtube_unavailable');
        }

        return ApiResponse::ok($summary);
    }

    public static function pin(array $params): ApiResponse
    {
        $video = Videos::changePinned((int) $params['id']);
        if (!$video) {
            throw ApiException::notFound('That video was not found.');
        }
        return ApiResponse::ok(Resource::video($video));
    }

    public static function update(array $params): ApiResponse
    {
        $id = (int) $params['id'];
        self::assertExists($id);

        $title = trim((string) (ApiRequest::json()['title'] ?? ''));
        if ($title === '') {
            throw ApiException::validation(['title' => 'Enter a title.']);
        }

        Videos::update($title, $id);

        return ApiResponse::ok(Resource::video(Videos::getById($id)));
    }

    public static function destroy(array $params): ApiResponse
    {
        $id = (int) $params['id'];
        self::assertExists($id);

        Videos::softDelete($id);

        return ApiResponse::noContent();
    }

    public static function restore(array $params): ApiResponse
    {
        $id = (int) $params['id'];
        self::assertExists($id);

        Videos::restore($id);

        return ApiResponse::noContent();
    }

    private static function assertExists(int $id): void
    {
        if (!Database::get('videos', ['id'], [], ['id' => $id])) {
            throw ApiException::notFound("Video #$id was not found.");
        }
    }
}
