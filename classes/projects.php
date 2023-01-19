<?php

class Projects{

    public static function loadprojects($limit = 3) {
        $results = database::getRows('projecten', ['removed'] , 's', [0], 'date DESC LIMIT ' . $limit);
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

    public static function addproject($target_dir, $target_file, $imageFileType, $filename, $name, $continue, $files, $title, $pgit, $plink) {
        $img = Projects::uploadimg($target_dir, $target_file, $imageFileType, $files);
        if($filename and $name) {
            $zip = Projects::uploadzip($filename, $name, $continue, $files);

        }else{
            $zip = true;
        }


        if($img == false) {
            echo "<script>alert('Sorry, there was an error uploading your img');</script>";
        } else if($zip == false) {
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
                if (isset($zip)) {
                    $link = substr($zip, 0, -4) . "/index";
                }
            } else {
                $link = $_POST["link"];
            }

            projects::dbaddproject($title, $git, $link, $img);
        }
    }

    public static function dbaddproject($name, $git, $link, $file) {
    Database::add('projecten', ['name','github','path','img'], 'ssss', [$name, $git, $link, $file]);
    }

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

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed');</script>";
            $uploadOk = 0;
            $target_file = "";
        } else{

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($files["img"]["tmp_name"], $target_file)) {
                    return $target_file;
//                    echo "<script>alert('The img has been uploaded.');</script>";
                } else {
                    return false;
//                    echo "<script>alert('Sorry, there was an error uploading your img');</script>";
                }
            }
        }

    }

    public static function uploadzip($filename, $name, $continue, $files) {
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
                echo "<script>alert('The file you are trying to upload is not a .zip file. Please try again');</script>";
            } else {

                $target_path = 'project/' . $filename;  // change this to the correct site path
                if (move_uploaded_file($source, $target_path)) {
                    $zip = new ZipArchive();
                    $x = $zip->open($target_path);
                    if ($x === true) {
                        $zip->extractTo('project/'); // change this to the correct site path
                        $zip->close();

                        unlink($target_path);
                    }
                    return $target_path;
                } else {
                    return false;
//                    echo "<script>alert('There was a problem with the upload. Please try again');</script>";
                }
            }
        }
    }
}
