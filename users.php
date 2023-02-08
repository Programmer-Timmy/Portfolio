<?php
include('requierd.php');
if (!isset($_SESSION['access'])) {
    header('location: account');
}
if ($_POST and isset($_GET['edit'])) {
    $result = accounts::loadaccount($_GET['edit']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    if ($_POST["password"] == "") {
        $password = $result['password'];
    };
    accounts::update($_GET["edit"], $password, $_POST["username"], $_POST["admin"]);
    header('location: /users');
}
if ($_POST and $_GET['add'] == 'account') {
    $return = accounts::add($_POST['password'], $_POST['username'], $_POST['admin']);
    echo $return;
}

if (isset($_GET["id"])) {
    accounts::sdelete($_GET["id"]);
    header('location: /users');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <title>Account</title>
</head>

<body>
    <header>
        <div class="container">
            <a href="javascript:void(0);" class="icon" onclick=" MyFunction()">
                <i class="fa fa-bars"></i>
            </a>
            <a href="logout" class="icon" style="display:block;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
            <a href="?add=account" class="icon" style="display:block; margin-right:40px;">
                <i class="fa-solid fa-plus"></i>
            </a>


            <nav id="nav">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="add-project">Add project</a></li>
                    <li><a href="remove">Remove</a></li>
                    <?php if (isset($_SESSION["admin"])) {
                        echo '<li><a href="users">Users</a></li>';
                    } ?>
                </ul>
            </nav>
        </div>
    </header>
    <?php
    if (!isset($_SESSION['admin'])) {
        echo '<div class="welcome">
        <h1>Je hebt geen toegang</h1>
        
        </div>';
    } elseif (isset($_GET['edit'])) {
        $result = accounts::loadaccount($_GET['edit']);
        $check = "";
        if ($result['admin'] == true) {
            $check = "checked";
        }

        echo '<form method="post">
        <input type="text" name="username" value="' . $result['username'] . '">
        <input type="text" name="password" value="">
        <input type="hidden" value="0" name="admin">
        <input type="checkbox" name="admin" id="admin" value="1"' . $check . '>
        <input type="submit" value="Versturen">
    </form>';
    } elseif (isset($_GET['add']) == 'account') {

        echo '<div class="admin"><form method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>
        <label for="password">Password:</label>
        <input type="text" name="password" required><br>
        <label for="admin">Admin:</label>
        <input type="hidden" value="0" name="admin">
        <input type="checkbox" name="admin" id="admin" value="1" class="checkbox"><br>
        <input type="submit" value="Versturen">
    </form></div>';
    } else {
        $results = accounts::loadaccounts();
        foreach ($results as $result) {
            echo "<div class='admin'><div><h1>" . $result['username'] . "<a href='?id=" . $result['id'] . "' onclick='return confirm(\"weet je het zeker?\");'>X</a><a href='?edit=" . $result["id"] . "'>edit</a></h1></div></div>";
        }
    }
    ?>

</body>
<script src="js/nav.js"></script>

</html>