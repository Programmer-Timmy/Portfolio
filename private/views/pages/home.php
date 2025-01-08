<?php
$projects = Projects::loadProjects("3");
?>
<div class="container">
    <div class="welcome">
        <h1>Welcome!</h1>

        <div class="text px-lg-5">
            <p>My name is Tim and I am a developer. I have experience with PHP, C#, Python, TypeScript, SCSS, and more.
                I have worked on multiple projects, some of which you can see below. If you want to see more projects, click the button below.
            </p>
        </div>
        <a href="projects" class="btn btn-primary"> View More Projects</a>
    </div>
    <div class="borderp" style="color: red">
        <?php if ($projects): ?>
            <div class="row">
                <?php foreach ($projects as $project): ?>
                    <?= HtmlTemplateRenderer::getProjectCard($project); ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h1 class="text">No projects found</h1>
        <?php endif; ?>
    </div>
</div>