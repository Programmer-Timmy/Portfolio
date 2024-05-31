<?php

class Videos
{
    public static function getAll()
    {
        return Database::getAll('videos', ['*'], [], [], 'date DESC');
    }

}