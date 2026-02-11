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
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

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
            <th scope="col">Name</th>
            <th scope="col">Description</th>
            <th scope="col">Url</th>
            <th scope="col">Github</th>
            <th scope="col">Pinned</th>
            <th scope="col">In Progress</th>
            <th scope="col"></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($projects as $project): ?>
            <tr>
                <td><?= $project->name ?></td>
                <td><?= $project->description ?></td>
                <td><?= $project->path ?></td>
                <td><?= $project->github ?></td>
                <td><?= $project->pinned ? 'Yes' : 'No' ?></td>
                <td><?= $project->in_progress ? 'Yes' : 'No' ?></td>
                <td>
                    <a href="/admin/editProject/<?= $project->id ?>" class="btn btn-primary">Edit</a>
                </td>
                <td>
                    <a href="?delete=<?= $project->id ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $('tr').each(function () {
        let description = $(this).find('td:nth-child(2)').text();
        try {
            description = JSON.parse(description);
        } catch (error) {
            return;
        }
        var tempCont = document.createElement("div");
        (new Quill(tempCont)).setContents(description);
        const html = tempCont.getElementsByClassName("ql-editor")[0].innerHTML;

        tempCont.remove();

        $(this).find('td:nth-child(2)').html(html);
        if (html.length > 100) {
            $(this).find('td:nth-child(2)').html(html.substring(0, 100) + '...');
        }

    });
</script>