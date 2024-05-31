<?php

class Videos
{
    public static function getAll()
    {
        return Database::getAll('videos', ['*'], [], [], 'date DESC');
    }

    public static function get($videoId)
    {
        return Database::get('videos', ['*'], [], ['videoId' => $videoId]);
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

    public static function add(): void
    {
        try {
            $env = parse_ini_file(__DIR__ . '/../../portfolio.env');
            if (!$env) {
                throw new Exception('Failed to parse environment file.');
            }

            $API_Key = $env['API_KEY'];
            $ChannelId = $env['CHANNEL_ID'];
            $Max_Results = 30;

            $apiUrl = 'https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId=' . $ChannelId . '&maxResults=' . $Max_Results . '&key=' . $API_Key;
            $apiData = @file_get_contents($apiUrl);

            if (!$apiData) {
                throw new Exception('Invalid API key or channel ID.');
            }

            $videoList = json_decode($apiData);

            foreach ($videoList->items as $item) {
                if ($item->id->kind !== 'youtube#video') {
                    continue;
                }

                self::processVideoItem($item);
            }

            self::cleanupOldVideos($videoList->items);
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo $e->getMessage();
        }
    }

    /**
     * @throws ErrorException
     * @throws Exception
     */
    private static function processVideoItem($item): void
    {
        $video = self::get($item->id->videoId);
        $date = new DateTime($item->snippet->publishedAt);
        $formattedDate = $date->format('Y-m-d H:i:s');

        if ($video) {
            if ($video->title !== $item->snippet->title) {
                self::update($item->snippet->title, $video->id);
                error_log("Video " . $video->videoId . " has been updated");
            }
        } else {
            self::addVideo($item->snippet->title, $item->id->videoId, $formattedDate);
            error_log('Added: ' . $item->snippet->title);
        }
    }

    private static function cleanupOldVideos($videoItems): void
    {
        $videos = self::getAll();
        $videoIds = array_column($videoItems, 'id->videoId');

        foreach ($videos as $video) {
            if (!in_array($video->videoId, $videoIds)) {
                self::delete($video->id);
                error_log('Deleted video ID: ' . $video->id);
            } else {
                error_log('Match found for video ID: ' . $video->videoId . ', not deleting');
            }
        }
    }

}