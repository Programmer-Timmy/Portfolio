<?php
$projects = Projects::loadprojects("3");
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <title>Tim van der Kloet</title>
</head>

<body>
    <?php require_once 'includes/header.html' ?>
    <div class="welcome">
        <h1>Welcome!</h1>
    </div>
    <div class="borderp">
        <?php
        if ($projects) {
            foreach ($projects as $project) {
                echo "
            <div class='project-home'>
                <a href=" . $project['path'] . "><img src=" . $project['img'] . " class='img-size' alt=''></a>";

                if ($project['github'] !== "empty") {
                    echo "<h1><a class='github' href=" . $project['github'] . "><i class='fa fa-github' aria-hidden='true'></i></a>" . $project['name'] . "</h1>";
                } else {
                    echo "<h1>" . $project['name'] . "</h1>";
                }
                echo "</div>";
            }
        } else {
            echo "<h1>Helaas geen projecten</h1>";
        }
        ?>
    </div>
</body>
<script src="js/nav.js"></script>

</html>