<?php
ob_start();
$videos = Videos::getAll();

if (isset($_GET['changePinned'])) {
    Videos::changePinned($_GET['changePinned']);
    header('Location: /admin/videos');
    exit;
}

?>

// a table

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center">Videos</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-light table-hover table-striped">
                <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Date</th>
                    <th scope="col">Pinned</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($videos as $video): ?>
                    <tr>
                        <td><?= $video->title ?></td>
                        <td><?= $video->date ?></td>
                        <td><?= $video->pinned ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="?changePinned=<?= $video->id ?>" class="btn btn-primary">Change Pinned</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
