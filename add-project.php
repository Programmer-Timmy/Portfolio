<?php
session_start();

if ($_POST) {
    include 'uploadzip.php';

    include 'upload.php';
    $git = "";
    if($_POST["git"] == ""){
        $git = "empty";
    } else {
        $git = $_POST["git"];
    }
    $link = "";
    if($_POST["link"] == ""){
        $link = substr($target_path, 0, -4) . "/index";
    } else {
        $link = $_POST["link"];
    }

    $con = new PDO("mysql:host=localhost;dbname=portfolio", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $con->prepare("INSERT INTO projecten(name, github, path, img) VALUES (?, ?, ?, ?)");
    $stmt->bindValue(1, htmlspecialchars($_POST["name"]));
    $stmt->bindValue(2, htmlspecialchars($git));
    $stmt->bindValue(3, htmlspecialchars($link));
    $stmt->bindValue(4, htmlspecialchars($target_file));

    $stmt->execute();
}

?>

<!DOCTYPE html>
<html lang="NL">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=
    , initial-scale=1.0">
    <title>test</title>
</head>

<body>
    <?php
    if (!isset($_SESSION['loggedin'])) {
        header('location: account');
    } else {
        echo'<form method="post" enctype="multipart/form-data">
        Naam van het project:
        <input type="text" name="name" id="name" required><br>
        Github link:
        <input type="text" name="git" id="git"><br>
        Select image to upload:
        <input type="file" name="img" id="img" required><br>
        Select your project or link
        <input type="file" name="zip_file" id="img"><input type="text" name="link" id="link"><br>

        <input type="submit" value="Toevoegen" name="submit">
    </form>';
    }
    ?>
</body>

</html>