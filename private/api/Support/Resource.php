<?php

/**
 * Transforms raw DB rows (stdClass from the existing controllers) into the
 * stable, camelCased shapes the API exposes. Keeping this separate from the
 * controllers means the wire format never accidentally leaks a column rename.
 */
class Resource
{
    public static function projectSummary(object $p): array
    {
        return [
            'id' => (int) $p->id,
            'name' => $p->name,
            'image' => Media::image($p->img ?? null),
            'languages' => array_map([self::class, 'language'], self::listOf($p->project_languages ?? null)),
            'links' => [
                'repository' => (!($p->private_repo ?? false) && !empty($p->github)) ? $p->github : null,
                'live' => !empty($p->path) ? $p->path : null,
            ],
            'flags' => [
                'pinned' => (bool) ($p->pinned ?? false),
                'inProgress' => (bool) ($p->in_progress ?? false),
                'privateRepo' => (bool) ($p->private_repo ?? false),
            ],
            'createdAt' => self::date($p->date ?? null),
            'updatedAt' => self::date($p->updated ?? null),
        ];
    }

    public static function projectDetail(object $p): array
    {
        $contributors = array_map([self::class, 'contributor'], self::listOf($p->project_contributors ?? null));

        return array_merge(self::projectSummary($p), [
            'description' => self::delta($p->description ?? null),
            'excerpt' => self::deltaText($p->description ?? null),
            'gallery' => array_values(array_filter(array_map(
                static fn ($img) => Media::image($img->img ?? null),
                self::listOf(Projects::loadProjectImg($p->id)),
            ))),
            'contributors' => $contributors,
        ]);
    }

    public static function language(object $l): array
    {
        return [
            'name' => $l->name,
            'color' => $l->color ?? null,
            'percentage' => isset($l->percentage) && $l->percentage !== null ? (float) $l->percentage : null,
        ];
    }

    public static function contributor(object $c): array
    {
        return [
            'login' => $c->login ?? null,
            'avatarUrl' => $c->avatar_url ?? null,
            'profileUrl' => $c->html_url ?? null,
            'contributions' => (int) ($c->contributions ?? 0),
        ];
    }

    public static function openSourceProject(object $p): array
    {
        return [
            'id' => (int) $p->id,
            'name' => $p->name,
            'description' => !empty($p->description) ? $p->description : null,
            'repositoryUrl' => $p->url ?? null,
            'pullRequestCount' => isset($p->pr_count) ? (int) $p->pr_count : null,
        ];
    }

    public static function pullRequest(object $pr): array
    {
        return [
            'id' => (int) $pr->id,
            'title' => $pr->title,
            'url' => $pr->url,
            'status' => $pr->status,
            'description' => !empty($pr->description) ? $pr->description : null,
            'createdAt' => self::date($pr->date ?? null),
        ];
    }

    public static function video(object $v): array
    {
        return [
            'id' => (int) $v->id,
            'title' => $v->title,
            'youtubeId' => $v->videoId,
            'url' => 'https://www.youtube.com/watch?v=' . $v->videoId,
            'embedUrl' => 'https://www.youtube.com/embed/' . $v->videoId,
            'thumbnailUrl' => 'https://i.ytimg.com/vi/' . $v->videoId . '/hqdefault.jpg',
            'pinned' => (bool) ($v->pinned ?? false),
            'publishedAt' => self::date($v->date ?? null),
        ];
    }

    // helpers

    /** Normalises the controllers' `false | array` returns to a list. */
    private static function listOf(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /** Quill delta stored as a JSON string -> array, or null. */
    private static function delta(?string $raw): ?array
    {
        if (!$raw) {
            return null;
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : null;
    }

    /** Flattened plain text of a Quill delta, for previews / meta descriptions. */
    private static function deltaText(?string $raw): ?string
    {
        $delta = self::delta($raw);
        if ($delta === null) {
            return $raw ? trim($raw) : null;
        }

        $text = '';
        foreach ($delta as $op) {
            if (is_array($op) && isset($op['insert']) && is_string($op['insert'])) {
                $text .= $op['insert'];
            }
        }
        $text = trim(preg_replace('/\s+/', ' ', $text));
        return $text !== '' ? $text : null;
    }

    private static function date(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return (new DateTime($value))->format(DateTime::ATOM);
        } catch (Exception) {
            return null;
        }
    }
}
