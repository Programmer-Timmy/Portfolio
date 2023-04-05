<?php
include '../requierd.php';
if ($_POST) {
    $return = accounts::Login($_POST['password'], $_POST['username']);
    echo $return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <title>Account</title>
</head>

<body>
    <?php
    if (!isset($_SESSION['access'])) {
        echo '<div class="admin"><form action="" method="post">
            username<br>
            <input type="text" name="username" id="username" required><br>
            password<br>
            <input type="password" name="password" id="password" required><br>
            <input type="submit" value="Inlogen">
            </form></div>';
    } else {
        require_once 'header.php';
        echo '
        <div class="welcome">
        <h1>Welcome!</h1>
        <h2>You are logged in!</h2>
        </div>';
        accounts::delete();
    }
    ?>

</body>
<script src="../js/nav.js"></script>
</html>