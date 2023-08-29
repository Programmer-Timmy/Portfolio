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
                    if ($video['title'] != $item->snippet->title) {
                        videos::update($item->snippet->title, $video['id']);
                        print_r("video " . $video['videoId'] . "geupdate");
                    }
                }
            } else {
                if ($item->id->videoId) {

                    Database::add('videos', ['title', 'videoId', 'date'], 'sss', [$item->snippet->title, $item->id->videoId, $formattedDate]);
                    print_r('toegevoegd:     ' . $item->snippet->title);
                }
            }
        }
        $videos = videos::getall();
        $foundMatch = false;
        foreach ($videos as $video) {
            foreach ($videoList->items as $item) {
                if ($item->id->videoId == $video['videoId']) {
                    $foundMatch = true;
                    break; // Exit the loop since a match is found
                }

            }
            if (!$foundMatch) {
                videos::delete($video['id']);
            } else {
                print_r('Match found, not deleting');
            }


        }


    }

    public static function get($videoId)
    {
        return database::getRow('videos', ['videoId'], 's', [$videoId]);
    }

    public static function getall()
    {
        return database::getRows('videos', false, false, false, 'date desc');
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