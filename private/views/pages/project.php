<?php
if (!isset($_GET['id'])) {
    header('Location: /projects');
    exit;
}

$project = Projects::loadProject($_GET['id']);
if (!$project) {
    header('Location: /projects');
    exit;
}
?>
<div class="container pb-5 single-projects">
    <div class="welcome">
        <h1><?= $project->name ?></h1>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="img-container">
                <img src="<?= $project->img ?>" class="img-size" alt="">
            </div>
        </div>
        <div class="col-lg-4">
            <div class="project-home">
                <div class="text">
                    <?= GlobalUtility::unpackDescription($project->description) ?>
                </div>
                <div class="text">
                    <p>Created at: <?= $project->date ?></p>
                </div>
                <div class="d-flex justify-content-evenly">
                    <?php if ($project->github): ?>
                        <a class="btn btn-github" href="<?= $project->github ?>">
                            <i class="fa fa-github" aria-hidden="true"></i>
                            Visit GitHub
                        </a>
                    <?php endif; ?>
                    <?php if ($project->path): ?>
                        <a class="btn btn-primary" href="<?= $project->path ?>">
                            <i class="fa fa-globe" aria-hidden="true"></i>
                            Visit Project
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
