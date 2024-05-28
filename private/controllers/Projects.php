<?php

class Projects
{

    public static function loadProjects($limit)
    {
        $results = Database::getAll('projects', ['*'], [], ['removed' => 0], 'date DESC limit ' . $limit);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    public static function loadProject($id)
    {
        $results = Database::get('projects', ['*'], [], ['id' => $id, 'removed' => 0]);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    public static function addProject($name, $description, $path,$github, $files)
    {   $date = date('Y-m-d H:i:s');
        $img = self::uploadImage($files);
        if (isset($img['error'])) {
            if (is_array($img['images'])) {
                foreach ($img['images'] as $image) {
                    self::deleteImage($image);
                }
            }

            return $img['error'];
        }
        $results = Database::insert('projects', ['name', 'description', 'date','path','github', 'img'], [$name, $description, $date, $path, $github, $img[0]]);
        if (!$results) {
            self::deleteImage($img[0]);
            return "There was an error adding your project.";
        }
        return "";


    }

    public static function uploadImage($files)
    {
        $urlArray = [];
        $count = 0;
        foreach ($files as $file) {
            $file_name = $file['name'];
            $file_name = explode(':', $file_name);
            $file_name = $file_name[0];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_error = $file['error'];
            $file_ext = explode('.', $file_name);
            $file_ext = strtolower(end($file_ext));

            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_ext, $allowed)) {
                if ($file_error === 0) {
                    if ($file_size <= 2097152) {
                        $file_name_new = uniqid('', true) . '.' . $file_ext;
                        $file_destination = '../public/img/' . $file_name_new;
                        if (move_uploaded_file($file_tmp, $file_destination)) {
                            $urlArray[$count] = "img/".$file_name_new;
                            $count++;
                        } else {
                            return ["error" => "There was an error uploading your image number " . $count, "images" => $urlArray];
                        }
                    } else {
                        return ["error" => "Your image " . $count . " is too large.", "images" => $urlArray];
                    }
                } else {
                    return ["error" => "There was an error uploading your image number " . $count, "images" => $urlArray];
                }
            } else {
                return ["error" => "File type of file number " . $count . " is not allowed.", "images" => $urlArray];
            }

        }
        return $urlArray;
    }

    private static function deleteImage($img)
    {
        if (file_exists($img)) {
            unlink('../public/' . $img);
        }
    }

    public static function deleteProject($id)
    {
        try {
            Database::update('projects', ['removed'], [1], ['id' => $id]);
            return "";
        } catch (Exception $e) {
            return "There was an error removing your project.";
        }

    }

    public static function hardDeleteProject($id)
    {
        try {
        Database::delete('projects', ['id' => $id]);
        return "";
        } catch (Exception $e) {
            return "There was an error removing your project.";
        }
    }

    public static function updateProject($name, $description, $path, $github, $files, $id)
    {
        $existing = Database::get('projects', ['img'], [], ['id' => $id]);
        $img = [$existing->img];
        if (!empty($files)){
            self::deleteImage($img[0]);

            $img = self::uploadImage($files);
            if (isset($img['error'])) {
                if (is_array($img['images'])) {
                    foreach ($img['images'] as $image) {
                        self::deleteImage($image);
                    }
                }
                return $img['error'];
            }
        }
        try {
            Database::update('projects', ['name', 'description', 'path', 'github','img'], [$name, $description, $path, $github, $img[0]], ['id' => $id]);
        } catch (Exception $e) {
            self::deleteImage($img[0]);
            return "There was an error updating your project.";
        }

        return "";
    }
}