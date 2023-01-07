<?php

if ($_POST) {

    $con = new PDO("mysql:host=localhost;dbname=portfolio", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $con->prepare("INSERT INTO projecten(name, paht, img) VALUES (?, ?, ?)");
    $stmt->bindValue(1, htmlspecialchars($_POST["name"]));
    $stmt->bindValue(2, htmlspecialchars($_POST["paht"]));
    $stmt->bindValue(3, htmlspecialchars($_POST["img"]));

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
    <form action="" method="post">
        <input type="text" id="name" name="name">
        <input type="file" name="" id="">
    </form>


</body>

</html>