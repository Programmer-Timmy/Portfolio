<?php

class videos
{
    public static function add($videoId, $title, $date){
        Database::add('videos', ['title', 'videoId', 'date'], 'sss', [$title, $videoId, $date]);
    }

    public static function get($videoId){
        return database::getRow('videos', ['videoId'], 's', [$videoId]);
    }

    public static function getall(){
        return database::getRows('videos');
    }

    public  static function update($title, $id){
        database::update('videos', $id, ['title'], 's', [$title]);
    }

    public static function delete($id){
        database::delete('videos', $id);
    }
}