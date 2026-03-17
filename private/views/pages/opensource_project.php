<?php
$project = OpenSource::getProject($_GET['id']);
if (!$project) {
    header('Location: /opensource');
    exit;
}
?>

<div class="container pb-5 single-projects">
    <div class="welcome mb-0">
        <h1><?= htmlspecialchars($project->name) ?></h1>
    </div>
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="mt-4">
               <h3 class="mb-4" style="color: #55d6aa;">My Contributions</h3>
               
               <?php if (!empty($project->prs)): ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($project->prs as $pr): ?>
                            <div class="p-4 rounded-3" style="background-color: #333; border: 1px solid #444; transition: transform 0.2s;">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <h5 class="mb-1">
                                        <a href="<?= htmlspecialchars($pr->url) ?>" target="_blank" class="text-decoration-none" style="color: #55d6aa;">
                                            <i class="fa fa-code-branch me-2"></i><?= htmlspecialchars($pr->title) ?>
                                        </a>
                                    </h5>
                                    <small class="text-muted text-nowrap ms-3"><?= date('M j, Y', strtotime($pr->date)) ?></small>
                                </div>
                                <p class="mb-2 mt-2" style="color: #ccc;"><?= htmlspecialchars($pr->description) ?></p>
                                <div class="mt-2">
                                    <?php 
                                    $statusClass = match(strtolower($pr->status)) {
                                        'merged' => 'bg-success',
                                        'open' => 'bg-success', 
                                        'closed' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($pr->status) ?></span>
                                    <a href="<?= htmlspecialchars($pr->url) ?>" target="_blank" class="ms-2 small text-decoration-none text-muted hover-white">View PR <i class="fa fa-external-link"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No contributions listed yet for this project.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-4 mt-4">
            <div class="project-home">
                <h3 style="color: #55d6aa;">Project Details</h3>
                <div class="text" id="description">
                    <p><?= nl2br(htmlspecialchars($project->description)) ?></p>
                </div>
                
                <div class="d-flex justify-content-evenly mt-4">
                    <a class="btn btn-github" target="_blank" href="<?= htmlspecialchars($project->url) ?>">
                        <i class="fa fa-github" aria-hidden="true"></i>
                        Visit Repository
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-white:hover { color: white !important; }
</style>
