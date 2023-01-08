<?php
session_start();

if (!isset($_SESSION['loggedin'])) {

    if ($_POST) {
        $con = new PDO("mysql:host=localhost;dbname=portfolio", "root", "");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $con->prepare("SELECT password FROM `account` WHERE username=?");
        $stmt->bindValue(1, htmlspecialchars($_POST["username"]));
        $stmt->execute();
        $info = $stmt->fetchAll(pdo::FETCH_OBJ);

        foreach ($info as $info) {
            $hash = $info->password;
            if (password_verify($_POST['password'], $hash)) {
                $hash = "";
                $_SESSION['loggedin'] = true;
            }
        }
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>account</title>
</head>

<body>
    <?php
    if (!isset($_SESSION['loggedin'])) {
        echo '<div class="admin"><form action="" method="post">
            username<br>
            <input type="text" name="username" id="username"><br>
            password<br>
            <input type="password" name="password" id="password"><br>
            <input type="submit" value="Inlogen">
            </form></div>';
    } else {
        echo '<header><div class="container"><a href="javascript:void(0);" class="icon" onclick="MyFunction()">
        <i class="fa fa-bars"></i>
        </a>
        <nav id="nav">
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="add-project">Add project</a></li>
            <li><a href="remove">Remove</a></li>
        </ul>
        </nav></div></header>
        <div class="welcome">
        <h1>Welcome!</h1>
        <h2>You are loged in!</h2>
        </div>

        ';
    }
    ?>

</body>
<script src="js/nav.js"></script>

</html>