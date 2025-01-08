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
                <?= HtmlTemplateRenderer::getProjectCard($project); ?>
            <?php endforeach; ?>
        <?php else: ?>
            <h1>No projects found</h1>
        <?php endif; ?>
    </div>
</div>
