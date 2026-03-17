<?php
$projects = OpenSource::getAll();

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (OpenSource::deleteProject($id) === "") {
        header('Location: /admin/opensource');
        exit;
    }
}
?>

<div class="container">
    <div class="welcome text-center mb-4">
        <h1>Admin Open Source</h1>
        <a href="/admin/addOpenSource" class="btn btn-primary mt-2">Add Project</a>
    </div>

    <table class="table table-light table-hover table-striped">
        <thead class="thead-dark">
        <tr>
            <th scope="col">Name</th>
            <th scope="col">URL</th>
            <th scope="col">PR Count</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if($projects): ?>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= htmlspecialchars($project->name) ?></td>
                    <td><a href="<?= htmlspecialchars($project->url) ?>" target="_blank"><?= htmlspecialchars($project->url) ?></a></td>
                    <td><?= isset($project->prs) ? count($project->prs) : 0 ?></td>
                    <td>
                        <a href="?delete=<?= $project->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure? This will delete the project and all associated PRs.')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No projects found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
