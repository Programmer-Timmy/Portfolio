<?php
$uploadOk = 1;

// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
  $check = getimagesize($_FILES["img"]["tmp_name"]);
  if ($check !== false) {
    $uploadOk = 1;
  } else {
    // echo "<script>alert('File is not an image');</script>";
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
    if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
      echo "<script>alert('The img has been uploaded.');</script>";
    } else {
      echo "<script>Sorry, there was an error uploading your img');</script>";
    }
  }
}
