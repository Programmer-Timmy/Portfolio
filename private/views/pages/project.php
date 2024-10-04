<?php
if (!isset($_GET['id'])) {
    header('Location: /projects');
    exit;
}

$project = Projects::loadProject($_GET['id']);
$images = Projects::loadProjectImg($_GET['id']);
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
            <div id="carouselExample" class="carousel slide carousel-dark carousel-fade" data-bs-ride="carousel"
                 data-bs-theme="dark">
                <?php if ($images): ?>
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                                class="active" aria-current="true" aria-label="Slide 1"></button>
                        <?php foreach ($images as $key => $image): ?>
                            <button type="button" data-bs-target="#carouselExampleIndicators"
                                    data-bs-slide-to="<?= $key + 1 ?>" aria-label="Slide <?= $key + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="carousel-inner">
                    <div class="carousel-item active img-container">
                        <img src="<?= $project->img ?>" class="d-block w-100" alt="...">
                    </div>
                    <?php if ($images): ?>
                        <?php foreach ($images as $key => $image): ?>
                            <div class="carousel-item img-container">
                                <img src="<?= $image->img ?>" class="d-block w-100" alt="...">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php if ($images): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="project-home">
                <div class="text">
                    <?= GlobalUtility::unpackDescription($project->description) ?>
                </div>
                <div class="text">
                    <p>Created at: <?= date_format(date_create($project->date), 'F j, Y'); ?></p>
                </div>
                <?php if ($project->project_languages): ?>
                    <div class="languages d-flex pb-3 justify-content-center flex-wrap">
                        <?php foreach ($project->project_languages as $language): ?>
                            <span class="badge bg-primary mx-1" style="background-color: <?= $language->color ?> !important; color: black;"><?= $language->name ?><?php if ($language->percentage):?> | <?= $language->percentage * 1 ?>%<?php endif?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-evenly">
                    <?php if ($project->github): ?>
                        <a class="btn btn-github" target="_blank" href="<?= $project->github ?>">
                            <i class="fa fa-github" aria-hidden="true"></i>
                            Visit on Github
                        </a>
                    <?php endif; ?>
                    <?php if ($project->path): ?>
                        <a class="btn btn-primary" target="_blank" href="<?= $project->path ?>">
                            <i class="fa fa-globe" aria-hidden="true"></i>
                            Visit Project
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
