<?php

class Projects
{

    public static function loadProjects($limit)
    {
        $results = database::getAll('projects', ['*'], [], ['removed' => 0], 'date DESC limit ' . $limit);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }
}