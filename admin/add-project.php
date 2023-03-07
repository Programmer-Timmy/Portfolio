<?php
require("../requierd.php");

if (!isset($_SESSION['access'])) {
    header('location: ../admin');
}

if ($_POST) {
    if ($_POST["link"] !== "") {
        $continue = true;
        $filename = false;
        $name = false;
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
    } elseif (!$continue) {
        echo '<script>alert("Your uploaded file is not a zip");</script>';
    } else {
        projects::addproject($target_dir, $target_file, $imageFileType, $filename, $name, $continue, $_FILES, $_POST["name"], $_POST["git"], $_POST["link"]);
    }
}
?>

<!DOCTYPE html>
<html lang="NL">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>

    <title>Add project</title>
</head>

<body>
    <?php require_once 'header.php'; ?>
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
<script src="../js/nav.js"></script>

</html>