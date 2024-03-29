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
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <title>Tim van der Kloet</title>
</head>

<body>
<?php require_once 'includes/Header.html' ?>
<div class="welcome">
    <h1>Welcome!</h1>
</div>
<div class="borderp">
    <?php
    if ($projects) {
        foreach ($projects as $project) {
            $popupdata = [$project['path'], $project['name'], $project['description'], $project['guest_password'], $project['guest_username']];
            $json = json_encode($popupdata);
            echo '
            <div class=\'project-home\'>
            <div>
                <a href=\'#\' onclick=\'showPopup(' . $json . ')\'>
                    <img src=\' ' . $project['img'] . ' \' class=\'img-size\' alt=\'\'>
                </a>
            </div>';

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
    <p id="popup-description"></p>
    <h3 id="login-title">Login information</h3>
    <ul>
        <li id="guest_username">Guest username:</li>
        <li id="guest_password">Guest password:</li>
    </ul>
    <button class="close-button" onclick="closePopup()">Close</button>
    <a href="" id="show-button">Show project</a>
</div>
</body>
<script src="js/nav.js"></script>
<script src="js/popup.js"></script>
</html>