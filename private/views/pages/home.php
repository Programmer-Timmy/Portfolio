<?php
$projects = Projects::loadProjects("3");
?>
<main class="container">
    <header class="welcome">
        <h1 class="h2">Welcome!</h1>

        <div class="text px-lg-5">
            <p>My name is Tim and I am a developer. I have experience with PHP, C#, Python, TypeScript, SCSS, and more.
                I have worked on multiple projects, some of which you can see below. If you want to see more projects, click the button below.
            </p>
        </div>
        <a href="projects" class="btn btn-primary"> View More Projects</a>
    </header>
    <section class="borderp" style="color: red">
        <?php if ($projects): ?>
            <div class="row">
                <?php foreach ($projects as $project): ?>
                    <?= HtmlTemplateRenderer::getProjectCard($project); ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h2 class="text h3">No projects found</h2>
        <?php endif; ?>
    </section>
</main>