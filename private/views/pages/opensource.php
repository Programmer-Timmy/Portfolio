<?php
$projects = OpenSource::getAll();
?>
<main class="container">
    <header class="welcome">
        <h1 class="h2">Open Source Contributions</h1>
        <div class="text">
            <p>I believe in the power of open source software. Here are some of the projects I've contributed to. Click on a project to see more information.</p>
        </div>
        <a href="https://github.com/Programmer-Timmy" target="_blank" class="btn btn-github">
            <i class="fa fa-github" aria-hidden="true"></i> Visit My GitHub
        </a>
    </header>

    <section class="row mt-4">
        <?php if ($projects): ?>
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <article class="card h-100 shadow-sm project-card" style="background-color: #333; border: 1px solid #444;">
                        <div class="card-body">
                            <h2 class="h5 card-title">
                                <a href="/opensource/<?= $project->id ?>" class="text-decoration-none stretched-link" style="color: #55d6aa;">
                                    <i class="fa fa-github-alt me-2"></i><?= htmlspecialchars($project->name) ?>
                                </a>
                            </h2>
                            <p class="card-text mt-2" style="color: #ccc;">
                                <?= !empty($project->description) ? htmlspecialchars($project->description) : 'No description available.' ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                            <span class="badge" style="background-color: #55d6aa; color: black;">
                                <i class="fa fa-code-branch me-1"></i><?= $project->pr_count ?? 0 ?> PRs
                            </span>
                            
                            <a href="<?= htmlspecialchars($project->url) ?>" target="_blank" class="btn btn-sm hover-teal" style="border: 1px solid #55d6aa; color: #55d6aa; position: relative; z-index: 2; border-radius: 20px; padding: 2px 10px; background: transparent;" title="Visit Repository">
                                <i class="fa fa-github me-1"></i> Visit Repository
                            </a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="welcome">
                    <h2 class="h3">No open source contributions found</h2>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<style>
.project-card {
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out, border-color 0.3s ease-in-out;
}
.project-card:hover {
    transform: scale(1.015);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.5)!important;
    border-color: #55d6aa !important;
}
.hover-teal:hover {
    background-color: #55d6aa !important;
    color: black !important;
}
</style>
