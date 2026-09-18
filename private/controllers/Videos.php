<?php

class Videos
{
    public static function getAll()
    {
        return Database::getAll('videos', ['*'], [], [], 'pinned DESC, date DESC');
    }

    public static function get($videoId)
    {
        return Database::get('videos', ['*'], [], ['videoId' => $videoId]);
    }

    public static function getById($id)
    {
        return Database::get('videos', ['*'], [], ['id' => $id]);
    }

    public static function update($title, $id): void
    {
        Database::update('videos', ['title'], [$title] , ['id' => $id]);
    }

    /**
     * @throws ErrorException
     */
    public static function addVideo($title, $videoId, $date): ?int
    {
        return Database::insert('videos', ['title', 'videoId', 'date'], [$title, $videoId, $date]);
    }

    public static function delete($id): void
    {
        Database::delete('videos', ["id" => $id]);
    }

    /** Hide a video from the site without removing the record. */
    public static function softDelete($id): void
    {
        Database::update('videos', ['deleted'], [1], ['id' => $id]);
    }

    public static function restore($id): void
    {
        Database::update('videos', ['deleted'], [0], ['id' => $id]);
    }

    /**
     * Pull the latest videos from the channel, upsert them, and prune ones that
     * have dropped off the channel.
     *
     * @return array{added:int, updated:int, deleted:int}
     * @throws Exception when the YouTube API can't be reached / is misconfigured
     */
    public static function add(): array
    {
        $apiKey = Env::get('API_KEY');
        $channelId = Env::get('CHANNEL_ID');
        if (!$apiKey || !$channelId) {
            throw new Exception('The YouTube API key or channel id is not configured.');
        }

        $apiUrl = 'https://www.googleapis.com/youtube/v3/search?order=date&part=snippet'
            . '&channelId=' . urlencode($channelId)
            . '&maxResults=30&key=' . urlencode($apiKey);
        $apiData = @file_get_contents($apiUrl);
        if (!$apiData) {
            throw new Exception('Could not reach the YouTube API. Check the API key and channel id.');
        }

        $videoList = json_decode($apiData);
        if (!$videoList || !isset($videoList->items)) {
            $message = $videoList->error->message ?? 'The YouTube API returned an unexpected response.';
            throw new Exception($message);
        }

        $added = 0;
        $updated = 0;
        foreach ($videoList->items as $item) {
            if (($item->id->kind ?? '') !== 'youtube#video') {
                continue;
            }
            $result = self::processVideoItem($item);
            if ($result === 'added') {
                $added++;
            } elseif ($result === 'updated') {
                $updated++;
            }
        }

        $deleted = self::cleanupOldVideos($videoList->items);

        return ['added' => $added, 'updated' => $updated, 'deleted' => $deleted];
    }

    /**
     * @return 'added'|'updated'|null
     * @throws ErrorException
     * @throws Exception
     */
    private static function processVideoItem($item): ?string
    {
        $video = self::get($item->id->videoId);
        $formattedDate = (new DateTime($item->snippet->publishedAt))->format('Y-m-d H:i:s');

        if ($video) {
            if ($video->title !== $item->snippet->title) {
                self::update($item->snippet->title, $video->id);
                return 'updated';
            }
            return null;
        }

        self::addVideo($item->snippet->title, $item->id->videoId, $formattedDate);
        return 'added';
    }

    private static function cleanupOldVideos($videoItems): int
    {
        $channelIds = [];
        foreach ($videoItems as $item) {
            $channelIds[] = $item->id->videoId ?? null;
        }

        $deleted = 0;
        foreach (self::getAll() as $video) {
            if (!in_array($video->videoId, $channelIds, true)) {
                self::delete($video->id);
                $deleted++;
            }
        }
        return $deleted;
    }

    /** @return object|null the updated row */
    public static function changePinned($id): ?object
    {
        $video = self::getById($id);
        if (!$video) {
            return null;
        }
        Database::update('videos', ['pinned'], [$video->pinned ? 0 : 1], ['id' => $id]);
        return self::getById($id);
    }

}