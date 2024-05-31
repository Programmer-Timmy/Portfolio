<?php
$error = "";
$projects = Projects::loadProjects("100");

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($error = Projects::deleteProject($id) === "") {
        header('Location: /admin/projects');
        exit;
    }
}
?>

<div class="container">
    <div class="welcome text-center mb-4">
        <h1>Admin Projects</h1>
        <a href="/admin/addProject" class="btn btn-primary mt-2">Add Project</a>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <?= $error ?>
        </div>
    <?php endif; ?>
    <table class="table table-light table-hover table-striped">
        <thead class="thead-dark">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Description</th>
            <th scope="col">Url</th>
            <th scope="col">Github</th>
            <th scope="col">Pinned</th>
            <th scope="col">Image</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($projects as $project): ?>
            <tr>
                <th scope="row"><?= $project->id ?></th>
                <td><?= $project->name ?></td>
                <td><?= $project->description ?></td>
                <td><?= $project->path ?></td>
                <td><?= $project->github ?></td>
                <td><?= $project->pinned ? 'Yes' : 'No' ?></td>
                <td><img src="/<?= $project->img ?>" class="img-size" alt="" width="30" height="30"></td>

                <td>
                    <a href="/admin/editProject?id=<?= $project->id ?>" class="btn btn-primary">Edit</a>
                    <a href="?delete=<?= $project->id ?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>