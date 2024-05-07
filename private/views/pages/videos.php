<?php
$videos = Videos::getall();
?>
<div class="welcome">
    <h1>Video's</h1>
</div>
<div class="borderp">
    <?php

    foreach ($videos as $item) {

        echo '
            <div id="videoborder">
            <div class="videos">
            
                <iframe class="video" src="https://www.youtube.com/embed/' . $item->videoId . '" frameborder="0" allowfullscreen></iframe>
                </div>
            <h1>' . $item->title . '</h1> 
                </div>';

    }

    ?>
</div>