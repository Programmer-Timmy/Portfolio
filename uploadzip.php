<?php
if ($_FILES["zip_file"]["name"]) {
    $source = $_FILES["zip_file"]["tmp_name"];
    $type = $_FILES["zip_file"]["type"];

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
            echo "<script>alert('Your .zip file was uploaded and unpacked');</script>";
        } else {
            echo "<script>alert('There was a problem with the upload. Please try again');</script>";
        }
    }
}
