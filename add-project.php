<?php
include("requierd.php");

if ($_SESSION['access'] != "logged") {
    header('location: account');
}


if ($_POST) {
    if ($_POST["link"] !== "") {
        $continue = true;
    } else {
        $filename = $_FILES["zip_file"]["name"];
        $name = explode(".", $filename);
        $continue = strtolower($name[1]) == 'zip' ? true : false;
    }

    $target_dir = "img/";
    $target_file = $target_dir . basename($_FILES["img"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo '<script>alert("Your uploaded file is not a img");</script>';
    } elseif ($continue != true) {
        echo '<script>alert("Your uploaded file is not a zip");</script>';
    } else {
        include 'uploadzip.php';

        include 'upload.php';

        $git = "";
        if ($_POST["git"] == "") {
            $git = "empty";
        } else {
            $git = $_POST["git"];
        }
        $link = "";
        if ($_POST["link"] == "") {
            if (isset($target_path)) {
                $link = substr($target_path, 0, -4) . "/index";
            }
        } else {
            $link = $_POST["link"];
        }
        Database::add($projecten, ['name','github','path','img'], 'ssss', [$_POST["name"], $git, $link, $target_file]);
    }
}
?>

<!DOCTYPE html>
<html lang="NL">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>test</title>
</head>

<body>
    <header>
        <div class="container"><a href="javascript:void(0);" class="icon" onclick="MyFunction()">
                <i class="fa fa-bars"></i>
            </a>
            <nav id="nav">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="add-project">Add project</a></li>
                    <li><a href="remove">Remove</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="admin">
        <form method="post" enctype="multipart/form-data">
            Naam van het project:
            <input type="text" name="name" id="name" required><br>
            Github link:
            <input type="text" name="git" id="git"><br>
            Select image to upload:
            <input type="file" name="img" id="img" accept="image/png, image/jpeg" required><br>
            Select your project or link:
            <input type="file" name="zip_file" id="zip_file" accept=".zip,.rar,.7zip"><input type="text" name="link" id="link"><br>

            <input type="submit" value="Toevoegen" name="submit">
        </form>
    </div>
</body>
<script src="js/nav.js"></script>

</html>