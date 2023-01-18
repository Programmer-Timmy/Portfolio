<?php

class Projects{

    public static function loadprojects($limit = 3) {
        $results = database::getRows('projecten', FALSE , FALSE, FALSE, 'date DESC LIMIT ' . $limit);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    public static function loadproject($id = 0) {
        if($id == 0) {
            return false;
        }
        $results = database::getRow('projecten', ['id'], 's', [$id]);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }
    
    public static function addproject($name, $git, $link, $file) {
        $result = Database::add('projecten', ['name','github','path','img'], 'ssss', [$name, $git, $link, $file]);

    }
}
