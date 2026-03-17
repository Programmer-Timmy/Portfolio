<?php
$projects = Projects::loadProjects("100");
?>

<main class="container">
    <header class="welcome">
        <h1 class="h2">Projects</h1>
        <div class="text">
            <p>These are some of the projects I have worked on. Click on a project to see more information. If you want
                to see more projects, visit my GitHub.</p>
        </div>
        <a href="https://github.com/Programmer-Timmy" target="_blank" class="btn btn-github"><i class="fa fa-github"
                                                                                                aria-hidden="true"></i>
            Visit My GitHub</a>
    </header>

    <section class="borderp row">
        <?php if ($projects): ?>
            <?php foreach ($projects as $project): ?>
                <?= HtmlTemplateRenderer::getProjectCard($project, 'col-md-6 col-lg-4 col-xl-3 mb-4'); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <h2 class="h3">No projects found</h2>
        <?php endif; ?>
    </section>
</main>
