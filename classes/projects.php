<?php

class Projects {

    /**
     * @function
     * load projects on limit
     */

    public static function loadprojects($limit = 3) {
        $results = database::getRows('projecten', ['removed'], 's', [0], 'date DESC LIMIT ' . $limit);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * @function
     * load project by id
     */

    public static function loadproject($id = 0) {
        if ($id == 0) {
            return false;
        }
        $results = database::getRow('projecten', ['id'], 's', [$id]);
        if ($results) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * @function
     * start and end of the add project function
     */

    public static function addproject($target_dir, $target_file, $imageFileType, $filename, $name, $continue, $files, $title, $pgit, $plink) {
        $img = Projects::uploadimg($target_dir, $target_file, $imageFileType, $files);
        if ($filename and $name) {
            $zip = Projects::uploadzip($filename, $name, $continue, $files);

        } else {
            $zip = true;
        }

        if (!$img) {
            echo "<script>alert('Sorry, there was an error uploading your img');</script>";
        } elseif (!$zip) {
            echo "<script>alert('Your .zip file was not uploaded and unpacked');</script>";
        } else {
            $git = "";
            if ($pgit == "") {
                $git = "empty";
            } else {
                $git = $_POST["git"];
            }

            $link = "";
            if ($plink == "") {
                $link = substr($zip, 0, -4) . "/index";
            } else {
                $link = $_POST["link"];
            }
            projects::dbaddproject($title, $git, $link, $img);
        }
    }

    /**
     * @function
     * add project to databse
     */

    public static function dbaddproject($name, $git, $link, $file) {
        Database::add('projecten', ['name', 'github', 'path', 'img'], 'ssss', [$name, $git, $link, $file]);
    }

    /**
     * @function
     * Upload the given img
     */

    public static function uploadimg($target_dir, $target_file, $imageFileType, $files) {
        $uploadOk = 1;

        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($files["img"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "<script>alert('file already exists');</script>";
            $uploadOk = 0;
        }

        // Check if $uploadOk is not set to 0 by an error
        if ($uploadOk != 0) {
            if (move_uploaded_file($files["img"]["tmp_name"], $target_file)) {
                return $target_file;
            } else {
                return false;
            }
        }
    }

    /**
     * @function
     * upload the given zip and unpack
     */

    public static function uploadzip($filename, $name, $continue, $files) {
        if ($files["zip_file"]["name"]) {
            $source = $files["zip_file"]["tmp_name"];
            $type   = $files["zip_file"]["type"];

            $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
            foreach ($accepted_types as $mime_type) {
                if ($mime_type == $type) {
                    $okay = true;
                    break;
                }
            }

            if (!$continue) {
                echo "<script>alert('The file you are trying to upload is not a .zip file. Please try again');</script>";
            } else {
                $target_path = 'project/' . $filename;
                if (move_uploaded_file($source, $target_path)) {
                    $zip = new ZipArchive();
                    $x   = $zip->open($target_path);
                    if ($x === true) {
                        $zip->extractTo('project/');
                        $zip->close();
                        unlink($target_path);
                    }
                    return str_replace(' ', '%20', $target_path);
                } else {
                    return false;
                }
            }
        }
    }
}
