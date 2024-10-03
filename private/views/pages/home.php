<?php
$projects = Projects::loadProjects("3");
?>
<div class="container" style="max-width: 90%">
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
                    <div class="col-md-6 col-lg-4">
                        <div class="project-home">
                            <div class="position-relative">
                                <?php if ($project->pinned): ?>
                                    <i class="pinned position-absolute translate-middle p-2 bg-success border border-light rounded-circle fa-solid fa-thumbtack"></i>
                                <?php endif; ?>
                                <a href="project?id=<?= $project->id ?>">
                                    <img src="<?php echo $project->img; ?>" class="img-size" alt="">
                                </a>
                            </div>
                            <h1>
                                <?php if ($project->github): ?>
                                    <a class="github text-decoration-none" href="<?php echo $project->github; ?>">
                                        <i class="fa fa-github" aria-hidden="true"></i>
                                    </a>
                                <?php endif; ?>
                                <?php echo $project->name; ?>
                            </h1>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <h1>Helaas geen projecten</h1>
        <?php endif; ?>
    </div>
</div>
