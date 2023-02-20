<?php
include("requierd.php");

if (!isset($_SESSION['access'])) {
    header('location: account');
}
$projects = Projects::loadprojects("100");

if (isset($_GET["id"])) {
    projects::sdeleteproject($_GET["id"]);
    header('location: /remove');
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
    <title>Remove</title>
</head>

<body>
    <header>
        <div class="container">
            <a href="javascript:void(0);" class="icon" onclick="MyFunction()">
                <i class="fa fa-bars"></i>
            </a>
            <a href="logout" class="icon" style="display:block;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
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
    if ($projects)
        foreach ($projects as $project) {
            echo "<div class='admin'><div><h1>" . $project['name'] . "<a href='?id=" . $project['id'] . "' onclick='return confirm(\"weet je het zeker?\");'>X</a></h1></div></div>";
        }
    else {
        echo "<div class='admin'><div><h1> Er zijn geen projecten</h1></div></div>";
    }
    ?>
</body>
<script src="js/nav.js"></script>

</html>