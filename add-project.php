<?php

if ($_POST) {
    include 'uploadzip.php';

    include 'upload.php';

    $con = new PDO("mysql:host=localhost;dbname=portfolio", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $con->prepare("INSERT INTO projecten(name, path, img) VALUES (?, ?, ?)");
    $stmt->bindValue(1, htmlspecialchars($_POST["name"]));
    $stmt->bindValue(2, htmlspecialchars(substr($target_path, 0, -4) . "/index"));
    $stmt->bindValue(3, htmlspecialchars($target_file));

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
    <form method="post" enctype="multipart/form-data">
        Naam van het project:
        <input type="text" name="name" id="name"><br>
        Github link:
        <input type="link" name="github" id="github"><br>
        Select image to upload:
        <input type="file" name="img" id="img"><br>
        Select your project
        <input type="file" name="zip_file" id="img"><br>

        <input type="submit" value="Toevoegen" name="submit">
    </form>
</body>

</html>