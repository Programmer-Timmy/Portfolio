<?php
$projects = Projects::loadprojects("100");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Tim van der Kloet - Projects</title>
</head>

<body>
<?php require_once 'includes/header.html' ?>
<div class="welcome">
        <h1>Projects</h1>
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