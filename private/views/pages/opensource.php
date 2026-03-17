<?php
$projects = OpenSource::getAll();
?>
<div class="container">
    <div class="welcome">
        <h1>Open Source Contributions</h1>
        <div class="text">
            <p>I believe in the power of open source software. Here are some of the projects I've contributed to. Click on a project to see more information.</p>
        </div>
        <a href="https://github.com/Programmer-Timmy" target="_blank" class="btn btn-github">
            <i class="fa fa-github" aria-hidden="true"></i> Visit My GitHub
        </a>
    </div>

    <div class="row mt-5">
        <?php if ($projects): ?>
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm project-card">
                        <div class="card-body">
                            <h3 class="h5 card-title">
                                <a href="/opensource/<?= $project->id ?>" class="text-decoration-none text-dark stretched-link">
                                    <i class="fa fa-github-alt me-2 text-primary"></i><?= htmlspecialchars($project->name) ?>
                                </a>
                            </h3>
                            <p class="card-text text-muted mt-2">
                                <?= !empty($project->description) ? htmlspecialchars($project->description) : 'No description available.' ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                            <span class="badge bg-secondary">
                                <i class="fa fa-code-branch me-1"></i><?= $project->pr_count ?? 0 ?> PRs
                            </span>
                            <small class="text-muted"><i class="fa fa-chevron-right"></i></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No open source contributions added yet. Check back later!
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.project-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.project-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>
