<?php
$videos = Videos::getAll();
?>

<main class="container">
    <header class="welcome">
        <h1 class="h2">Videos</h1>

        <div class="text">
            <p>Here you can find all my droning videos. I have been droning for a while now and I have made some amazing
                videos. I hope you enjoy them as much as I do.</p>
        </div>
        <a href="https://www.youtube.com/@Tim-van-der-Kloet" target="_blank" class="btn btn-youtube"><i
                    class="fab fa-youtube" aria-hidden="true"></i> Visit My YouTube Channel</a>
    </header>
    <section class="borderp row">
        <?php foreach ($videos as $item): ?>
            <div class="col-lg-6">
                <div id="videoborder">
                    <div class="videos position-relative">
                        <div class="badgeContainer position-absolute">
                        <?php if ($item->pinned): ?>
                            <i class="pinned p-2 border border-light rounded-circle fa-solid fa-thumbtack"></i>
                        <?php endif; ?>
                        </div>
                        <iframe class="video" src="https://www.youtube.com/embed/<?= $item->videoId ?>"
                                frameborder="0"
                                allowfullscreen></iframe>
                    </div>
                    <h2 class="h4"><?= $item->title ?></h2>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
</main>


