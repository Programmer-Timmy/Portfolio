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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Home</title>
</head>

<body>
    <header>
        <div class="container">
            <h1 class="logo">Tim van der Kloet</h1>
            <a href="javascript:void(0);" class="icon" onclick="MyFunction()">
                <i class="fa fa-bars"></i>
            </a>
            <nav id="nav">
                <ul>
                    <li><a href="home">Home</a></li>
                    <li><a href="about">About</a></li>
                    <li><a href="contact">Contact</a></li>
                    <li><a href="projects">Projects</a></li>
                </ul>
            </nav>
        </div>
    </header>
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