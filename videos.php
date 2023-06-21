<?php

$API_Key = 'AIzaSyAR-UxFdmtwYTomJn5aM-sUjFZw5zg16Nc';
$ChanelId = 'UC48IsPEeWQ9MDqtmFBMd-2A';
$Max_Results = 30;

$apiData = @file_get_contents('https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId=' . $ChanelId . '&maxResults=' . $Max_Results . '&key=' . $API_Key . '');
if ($apiData) {
    $videoList = json_decode($apiData);
//    var_dump($videoList->items, $videoList->id);

} else {
    echo 'Invalid API key or channel ID.';
}

foreach ($videoList->items as $item) {
    $video = videos::get($item->id->videoId);
    $date = new DateTime($item->snippet->publishedAt);
    $formattedDate = $date->format('Y-m-d H:i:s');
    if($video) {
        if ($video['videoId'] == $item->id->videoId) {
            if ($video['title'] !== $item->snippet->title) {
                videos::update($item->snippet->title, $video['id']);
            }
        }
    }else {
        if($item->id->videoId) {

            videos::add($item->id->videoId, $item->snippet->title, $formattedDate);
        }
    }
}
$videos = videos::getall();
var_dump($videos);
foreach($videos as $video){
    $deleted = false;
    foreach ($videoList->items as $item){
        if($video['videoId'] == $item->id->videoId){
            $deleted = true;
        }
    }
    if($deleted = true){
//        videos::delete($video['id']);
    }
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
    <title>Tim van der Kloet - Video's</title>
</head>

<body>
<?php require_once 'includes/Header.html' ?>
<div class="welcome">
    <h1>Video's</h1>
</div>
<div class="borderp">
    <?php

        foreach ($videos as $item) {

            echo '
            <div>
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