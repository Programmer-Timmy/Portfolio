<?php

/**
 * projects
 */
class Projects
{

    /**
     * @function
     * load projects on limit
     * @param int $limit
     * @return array|false
     */

    public static function loadprojects($limit = 3)
    {
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
     * @param int $id
     * @return array|false|mixed
     */

    public static function loadproject($id = 0)
    {
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
     * @param $file
     * @param $title
     * @param $pgit
     * @param $plink
     * @return string|void
     */

    public static function addproject($file, $title, $pgit, $plink, $description, $password, $username)
    {
        $continue = false;
        if ($plink !== "") {
            $continue = true;
            $filename = false;
            $name = false;
        } else {
            $filename = $file["zip_file"]["name"];
            $name = explode(".", $filename);
            $continue = strtolower($name[1]) == 'zip' ? true : false;
        }

        $target_dir = "img/";
        $target_file = $target_dir . uniqid() . "_" . basename($file["img"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            return '<script>alert("Your uploaded file is not a img");</script>';
        } elseif (!$continue) {
            return '<script>alert("Your uploaded file is not a zip");</script>';
        }

        $img = Projects::uploadimg($target_dir, $target_file, $imageFileType, $file);
        if ($filename and $name) {
            $zip = Projects::uploadzip($filename, $name, $continue, $file);
        } else {
            $zip = true;
        }

        if (!$img) {
            return "<script>alert('Sorry, there was an error uploading your img');</script>";
        } elseif (!$zip) {
            return "<script>alert('Your .zip file was not uploaded and unpacked');</script>";
        } else {
            $git = $pgit;

            $link = "";
            if ($plink == "") {
                $link = substr($zip, 0, -4) . "/index";
            } else {
                $link = $plink;
            }


            projects::dbaddproject($title, $git, $link, $img, $description, $password, $username);
        }
    }

    /**
     * @function
     * update the project
     * @param $id
     * @param $name
     * @param $github
     */
    public static function update($id, $name, $github, $imgUrl, $oldPath, $file, $description, $password, $username)
    {
        if ($file['img']['name'] !== '') {
            if (file_exists($imgUrl)) {
                unlink($imgUrl);
            }

            $dataImg = self::uploadimg("img/", "img/" . uniqid() . "_" . basename($file["img"]["name"]), strtolower(pathinfo("img/", PATHINFO_EXTENSION)), $file);
            database::update('projecten', $id, ['name', 'github', 'img', 'description', 'guest_password', 'guest_username'], 'ssssss', [$name, $github, $dataImg, $description, $password, $username]);
        }
        if ($file["zip_file"]["name"] !== '') {
            $path = (substr($oldPath, 0, -6));
            self::deleteDirectory($path);
        }
        database::update('projecten', $id, ['name', 'github', 'description', 'guest_password', 'guest_username'], 'sssss', [$name, $github, $description, $password, $username]);


    }

    /**
     * @function
     * add project to databse
     * @param $name
     * @param $git
     * @param $link
     * @param $file
     */
    public static function dbaddproject($name, $git, $link, $file, $description, $password, $username)
    {
        Database::add('projecten', ['name', 'github', 'path', 'img', 'date', 'description', 'guest_password', 'guest_username'], 'ssssssss', [$name, $git, $link, $file, date('y-m-d h:m:s'), $description, $password, $username]);
    }

    /**
     * @function
     * Upload the given img
     * @param $target_dir
     * @param $target_file
     * @param $imageFileType
     * @param $files
     * @return array|false
     */
    private static function uploadimg($target_dir, $target_file, $imageFileType, $files)
    {
        $uploadOk = 1;

        // Check if image file is actual image or fake image
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
     * @param $filename
     * @param $name
     * @param $continue
     * @param $files
     * @return array|false
     */

    private static function uploadzip($filename, $name, $continue, $files)
    {
        if ($files["zip_file"]["name"]) {
            $source = $files["zip_file"]["tmp_name"];
            $type = $files["zip_file"]["type"];

            $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
            foreach ($accepted_types as $mime_type) {
                if ($mime_type == $type) {
                    $okay = true;
                    break;
                }
            }

            if (!$continue) {
                return "<script>alert('The file you are trying to upload is not a .zip file. Please try again');</script>";
            } else {
                $target_path = 'project/' . $filename;
                if (move_uploaded_file($source, $target_path)) {
                    $zip = new ZipArchive();
                    $x = $zip->open($target_path);
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

    /**
     * @function
     * Softdelete the project
     * @param $id
     */
    public static function sdeleteproject($id)
    {
        database::update('projecten', $id, ['removed'], 's', [1]);
    }

    /**
     * @function
     * full delete project
     */
    public static function delete()
    {
        $results = database::getRows('projecten', ['removed'], 's', [1]);
        foreach ($results as $result) {
            $date = date_create($result['updated']);
            date_add($date, date_interval_create_from_date_string("6 day"));
            $max_date = date_format($date, "y-m-d");
            if ($max_date <= date("y-m-d")) {
                Database::delete('projecten', $result['id']);

                $path = (substr($result['path'], 0, -6));
                unlink($result['img']);

                self::deleteDirectory($path);
            }

        }

    }

    private static function deleteDirectory($path)
    {
        if (is_dir($path)) {
            $objects = scandir($path);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($path . DIRECTORY_SEPARATOR . $object) == "dir") {
                        deleteDirectory($path . DIRECTORY_SEPARATOR . $object);
                    } else {
                        unlink($path . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }
            rmdir($path);
        }

    }
}


