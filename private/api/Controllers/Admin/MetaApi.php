<?php

namespace Admin;

use ApiResponse;
use Database;

/** Small lookups the admin forms need. */
class MetaApi
{
    /** GET /api/admin/languages — the programming_languages catalogue. */
    public static function languages(): ApiResponse
    {
        $rows = Database::getAll('programming_languages', ['id', 'name', 'color'], [], [], 'name ASC');

        $data = array_map(static fn ($l) => [
            'id' => (int) $l->id,
            'name' => $l->name,
            'color' => $l->color ?? null,
        ], is_array($rows) ? $rows : []);

        return ApiResponse::collection($data);
    }
}
