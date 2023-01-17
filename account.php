<?php
include('requierd.php');
    if ($_POST) {
        $account = $database::getRow('account', ['username'], 's', [$_POST['username']]);        
        if (!isset($account['password'])) {
            echo '<script>alert("Wrong username or password")</script>';
        } elseif (password_verify($_POST['password'], $account['password'])) {
            $_SESSION['access'] = "logged";
        } else {
            echo '<script>alert("Wrong username or password")</script>';
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
    <title>Account</title>
</head>

<body>
    <?php
    if ($_SESSION['access'] != "logged") {
        echo '<div class="admin"><form action="" method="post">
            username<br>
            <input type="text" name="username" id="username" required><br>
            password<br>
            <input type="password" name="password" id="password" required><br>
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
        <h2>You are logged in!</h2>
        </div>';
    }
    ?>

</body>
<script src="js/nav.js"></script>

</html>