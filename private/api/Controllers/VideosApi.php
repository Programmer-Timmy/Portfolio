<?php

class VideosApi
{
    public static function index(): ApiResponse
    {
        $videos = Videos::getAll();
        $videos = is_array($videos) ? $videos : [];

        // The videos table has a soft-delete flag the controller doesn't filter.
        $videos = array_filter($videos, static fn ($v) => !($v->deleted ?? false));

        $limit = ApiRequest::int('limit', 0);
        if ($limit > 0) {
            $videos = array_slice(array_values($videos), 0, $limit);
        }

        $data = array_map([Resource::class, 'video'], array_values($videos));

        return ApiResponse::collection($data, ['count' => count($data)]);
    }
}
