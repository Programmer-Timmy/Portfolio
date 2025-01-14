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
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<div class="container pb-5 single-projects">
    <div class="welcome mb-0">
        <h1><?= $project->name ?></h1>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <container id="project-images" class="">
                <div id="carouselExample" class="carousel slide carousel-dark carousel-fade mt-4" data-bs-ride="carousel"
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
                            <img src="https://portfolio.timmygamer.nl/<?= $project->img ?>" class="d-block w-100" alt="..." loading="lazy">
                        </div>
                        <?php if ($images): ?>
                            <?php foreach ($images as $key => $image): ?>
                                <div class="carousel-item img-container">
                                    <img src="https://portfolio.timmygamer.nl/<?= $image->img ?>" class="d-block w-100" alt="..." loading="lazy">
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
            </container>
        </div>
        <div class="col-lg-4 mt-4">
            <div class="project-home">
                <?php if ($project->in_progress): ?>
                    <div class="badge in-progress ">
                        <i class="fa fa-person-digging" aria-hidden="true"></i>
                        Work in Progress
                    </div>
                <?php endif; ?>
                <div class="text" id="description">

                </div>
                <div class="text text-center">
                    <p>Created at: <?= date_format(date_create($project->date), 'F j, Y'); ?></p>
                </div>
                <?php if ($project->project_languages): ?>
                    <div class="languages d-flex pb-3 justify-content-center flex-wrap">
                        <?php foreach ($project->project_languages as $language): ?>
                            <?= HtmlTemplateRenderer::get_language_badge($language) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($project->project_contributors && count($project->project_contributors) > 1): ?>
                    <h3 class="text-center">Contributors</h3>
                    <div class="contributors d-flex pb-3 justify-content-center flex-wrap gap-2">
                        <?php foreach ($project->project_contributors as $contributor): ?>
                            <a href="<?= $contributor->html_url ?>" class="contributor" style="width: 40px; height: 40px;">
                                <img src="<?= $contributor->avatar_url ?>" alt="<?= $contributor->login ?>" class="rounded-circle" style="width: 40px; height: 40px;">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-evenly">
                    <?php if ($project->github && !$project->private_repo): ?>
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

<script>
    var tempCont = document.createElement("div");
    (new Quill(tempCont)).setContents(<?= $project->description ?>);
    const html = tempCont.getElementsByClassName("ql-editor")[0].innerHTML;

    tempCont.remove();
    $('#description').html(html);
</script>
