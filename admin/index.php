<?php
require_once '../includes/requierd.php';
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
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <title>Tim van der Kloet - Login</title>
</head>

<body>
<?php
if (!isset($_SESSION['access'])) {
    echo '<div class="admin"><form action="" method="post">
            Username:
            <input type="text" name="username" id="username" required>
            Password:
            <input type="password" name="password" id="password" required>
            <input type="submit" value="Login">
            </form></div>';
} else {
    require_once 'header.php';
    echo "
        <div class=\"welcome\">
        <h1>Welcome " . $_SESSION['name'] . "!</h1>
        <h2>You are logged in!</h2>
        </div>";
    accounts::delete();
}
?>

</body>
<script src="../js/nav.js"></script>
</html>