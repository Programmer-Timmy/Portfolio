<?php
$videos = videos::getall();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <title>Tim van der Kloet - Video's</title>
</head>

<body>
<?php require_once '../private/includes/Header.html' ?>
<div class="welcome">
    <h1>Video's</h1>
</div>
<div class="borderp">
    <?php

    foreach ($videos as $item) {

        echo '
            <div id="videoborder">
            <div class="videos">
            
                <iframe class="video" src="https://www.youtube.com/embed/' . $item["videoId"] . '" frameborder="0" allowfullscreen></iframe>
                </div>
            <h1>' . $item["title"] . '</h1> 
                </div>';

    }

    ?>
</div>
</body>
<script src="js/nav.js"></script>
</html>
