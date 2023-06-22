<?php

class videos
{
    public static function add()
    {
        $API_Key = 'AIzaSyAHCK_d4XHhixifCHz7ibRJQfLzXaBJ8xQ';
        $ChanelId = 'UC48IsPEeWQ9MDqtmFBMd-2A';
        $Max_Results = 30;

        $apiData = @file_get_contents('https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId=' . $ChanelId . '&maxResults=' . $Max_Results . '&key=' . $API_Key . '');
        if ($apiData) {
            $videoList = json_decode($apiData);
//    var_dump($videoList->items, $videoList->id);

        } else {
            echo 'Invalid API key or channel ID.';
        }

        foreach ($videoList->items as $item) {
            $video = videos::get($item->id->videoId);
            $date = new DateTime($item->snippet->publishedAt);
            $formattedDate = $date->format('Y-m-d H:i:s');
            if ($video) {
                if ($video['videoId'] == $item->id->videoId) {
                    if ($video['title'] !== $item->snippet->title) {
                        videos::update($item->snippet->title, $video['id']);
                    }
                }
            } else {
                if ($item->id->videoId) {

                    Database::add('videos', ['title', 'videoId', 'date'], 'sss', [$item->snippet->title, $item->id->videoId, $formattedDate]);

                }
            }
        }
        $videos = videos::getall();
        var_dump($videos);
        foreach ($videos as $video) {
            $deleted = false;
            foreach ($videoList->items as $item) {
                if ($video['videoId'] == $item->id->videoId) {
                    $deleted = true;
                }
            }
            if ($deleted = true) {
        videos::delete($video['id']);
            }
        }


    }

    public static function get($videoId)
    {
        return database::getRow('videos', ['videoId'], 's', [$videoId]);
    }

    public static function getall()
    {
        return database::getRows('videos');
    }

    public static function update($title, $id)
    {
        database::update('videos', $id, ['title'], 's', [$title]);
    }

    public static function delete($id)
    {
        database::delete('videos', $id);
    }
}