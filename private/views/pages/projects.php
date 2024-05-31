<?php
$projects = Projects::loadProjects("100");
?>

<div class="container">
    <div class="welcome">
        <h1>Projects</h1>
        <div class="text">
            <p>These are some of the projects I have worked on. Click on a project to see more information. If you want
                to see more projects, visit my GitHub.</p>
        </div>
        <a href="https://github.com/Programmer-Timmy" target="_blank" class="btn btn-github"><i class="fa fa-github"
                                                                                                aria-hidden="true"></i>
            Visit My GitHub</a>
    </div>

    <div class="borderp row">
        <?php if ($projects): ?>
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="project-home">
                        <div class="position-relative">
                            <?php if ($project->pinned): ?>
                                <i class="pinned position-absolute translate-middle p-2 bg-success border border-light rounded-circle fa-solid fa-thumbtack"></i>
                            <?php endif; ?>
                            <a href="project?id=<?= $project->id ?>">

                                <img src="<?= $project->img ?>" class="img-size" alt="">
                            </a>
                        </div>

                        <?php if ($project->github): ?>
                            <h1>
                                <a class="github text-decoration-none" href="<?= $project->github ?>">
                                    <i class="fa fa-github" aria-hidden="true"></i>
                                </a>
                                <?= $project->name ?>
                            </h1>
                        <?php else: ?>
                            <h1><?= $project->name ?></h1>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <h1>Helaas geen projecten</h1>
        <?php endif; ?>
    </div>
</div>
