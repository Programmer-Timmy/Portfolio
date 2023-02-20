<?php
include('../requierd.php');
if (!isset($_SESSION['access'])) {
    header('location: ../account');
}
if ($_POST and isset($_GET['edit'])) {
    $return = Projects::update($_GET['edit'], $_POST['name'], $_POST['github']);
    header('location: projects');
}

if (isset($_GET["id"])) {
    projects::sdeleteproject($_GET["id"]);
    header('location: projects');
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
    <title>Projects</title>
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
            <a href="../add-project.php" class="icon" style="display:block; margin-right:40px;">
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
    if (isset($_GET['edit'])) {
        $result = Projects::loadproject($_GET['edit']);

        echo '<div class="admin">
        <form method="post" enctype="multipart/form-data">
            Naam van het project:
            <input type="text" name="name" id="name" value="' . $result['name'] . '" required><br>
            Github link:
            <input type="text" name="github" id="git" value="'. $result['github'] .'"><br>
            <input type="submit" value="Update" name="submit">
            </div>
        </form>';
    } else {
        $results = Projects::loadprojects(100);
        foreach ($results as $result) {
            echo "<div class='admin'><div><h1>" . $result['name'] . "<a href='?id=" . $result['id'] . "' onclick='return confirm(\"weet je het zeker?\");'>X</a><a href='?edit=" . $result["id"] . "'>edit</a></h1></div></div>";
        }
    }
    ?>

</body>
<script src="js/nav.js"></script>

</html>