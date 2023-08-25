<?php
$projects = Projects::loadprojects("100");
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <title>Tim van der Kloet - Projects</title>
</head>
<div class="borderp">
    <?php
    if ($projects) {
        foreach ($projects as $project) {

            echo "
            <div class='project-home'>
                <div>
                    <a href='#' onclick=\"showPopup('" . $project['path'] . "',' " . $project['img'] . "',' " . $project['name'] . "',' " . ($project['description']) . "')\">
                        <img src='" . $project['img'] . "' class='img-size' alt=''>
                    </a href='#'>
                </div>";

            if ($project['github']) {
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

<div id="popup" class="popup">
    <h2 class="popup-title" id="popup-title"></h2>
    <div id="popup-description"></div>
    <button class="close-button" onclick="closePopup()">Close</button>
    <a href="" id="show-button" >Show project</a>
</div>

<script src="js/nav.js"></script>
<script src="js/popup.js"></script>