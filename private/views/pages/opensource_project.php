<?php
$project = OpenSource::getProject($_GET['id']);
if (!$project) {
    // Redirect or show 404
    header('Location: /opensource');
    exit;
}
?>

<div class="container">
    <div class="welcome mb-4">
        <a href="/opensource" class="text-decoration-none text-muted mb-2 d-inline-block"><i class="fa fa-arrow-left"></i> Back to Open Source</a>
        <h1><?= htmlspecialchars($project->name) ?></h1>
        <?php if (!empty($project->description)): ?>
            <p class="lead text-muted"><?= htmlspecialchars($project->description) ?></p>
        <?php endif; ?>
        <a href="<?= htmlspecialchars($project->url) ?>" target="_blank" class="btn btn-outline-dark mt-2">
            <i class="fa fa-github" aria-hidden="true"></i> View Repository
        </a>
    </div>

    <div class="mt-4">
        <h3 class="mb-3">My Contributions</h3>
        <?php if (!empty($project->prs)): ?>
            <div class="list-group shadow-sm">
                <?php foreach ($project->prs as $pr): ?>
                    <div class="list-group-item p-4">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <h5 class="mb-1">
                                <a href="<?= htmlspecialchars($pr->url) ?>" target="_blank" class="text-dark text-decoration-none">
                                    <i class="fa fa-code-branch me-2 text-primary"></i><?= htmlspecialchars($pr->title) ?>
                                </a>
                            </h5>
                            <small class="text-muted text-nowrap ms-3"><?= date('M j, Y', strtotime($pr->date)) ?></small>
                        </div>
                        <p class="mb-2 mt-2 text-secondary"><?= htmlspecialchars($pr->description) ?></p>
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
                            <a href="<?= htmlspecialchars($pr->url) ?>" target="_blank" class="ms-2 small text-decoration-none">View PR <i class="fa fa-external-link"></i></a>
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
