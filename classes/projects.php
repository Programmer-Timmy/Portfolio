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
    
}