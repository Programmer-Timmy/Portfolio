<?php
$videos = Videos::getAll();
?>

<div class="container">
    <div class="welcome">
        <h1>Videos</h1>

        <div class="text">
            <p>Here you can find all my droning videos. I have been droning for a while now and I have made some amazing videos. I hope you enjoy them as much as I do.</p>
        </div>
        <a href="https://www.youtube.com/@Tim-van-der-Kloet" target="_blank" class="btn btn-youtube">Visit My YouTube Channel</a>

        <div class="borderp row">
        <?php foreach ($videos as $item): ?>
            <div class="col-lg-6">
                <div id="videoborder">
                    <div class="videos">
                        <iframe class="video" src="https://www.youtube.com/embed/<?= $item->videoId ?>" frameborder="0"
                                allowfullscreen></iframe>
                    </div>
                    <h1><?= $item->title ?></h1>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</div>

