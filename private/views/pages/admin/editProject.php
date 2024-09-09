<?php
if (!isset($_GET['id'])) {
    header('Location: /admin/projects');
    exit;
}
$project = Projects::loadProject($_GET['id']);
if (!$project) {
    header('Location: /admin/projects');
    exit;
}

$error = '';
if ($_POST) {
    $descriptionArray = json_decode($_POST['description']);
    if (empty($_POST['title'])) {
        $error = 'Please enter a title';
    }
    if (count($descriptionArray) == 1 && strlen($descriptionArray[0]->insert) == 1) {
        $error = 'Please enter a description';
    }
    if (isset($_POST['pinned'])) {
        $_POST['pinned'] = 1;
    } else {
        $_POST['pinned'] = 0;
    }
    if (empty($error)) {
        $error = Projects::updateProject($_POST['title'], $_POST["description"], $_POST['link'], $_POST['github'], $_FILES, $_POST['pinned'], $project->id);
        if (empty($error)) {
            header('Location: /admin/projects');
        }
    }

}

?>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet"/>
<link
        rel="stylesheet"
        type="text/css"
        href="https://unpkg.com/file-upload-with-preview/dist/style.css"
/>
<div class="container mx-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="welcome text-center mb-4">
                <h3>Edit Project</h3>
            </div>
            <form method="post" style="color: #777" enctype="multipart/form-data">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                <div class="form-group py-2">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" <?php if (isset($_POST['title'])) {
                        echo 'value="' . $_POST['title'] . '"';
                    } else {
                        echo 'value="' . $project->name . '"';
                    } ?> required name="title" placeholder="Enter the title">
                    <div class="form-group py-2">
                        <label for="link">Link</label>
                        <input type="text" class="form-control" id="link" name="link"
                               placeholder="Enter the link" <?php if (isset($_POST['link'])) {
                            echo 'value="' . $_POST['link'] . '"';
                        } else {
                            echo 'value="' . $project->path . '"';
                        } ?>>
                    </div>
                    <div class="form-group py-2">
                        <label for="github">Github Link</label>
                        <input type="text" class="form-control" id="github" name="github"
                               placeholder="Enter the link" <?php if (isset($_POST['link'])) {
                            echo 'value="' . $_POST['github'] . '"';
                        } else {
                            echo 'value="' . $project->github . '"';
                        } ?>>
                    </div>
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input" type="checkbox" value="1" id="pinned" name="pinned"
                            <?php if (isset($_POST['pinned'])) {
                                echo 'checked';
                            } elseif ($project->pinned) {
                                echo 'checked';
                            }
                            ?>>
                        <label class="form-check label" for="pinned">Pinned</label>
                    </div>
                    <div class="form-group py-2">
                        <label for="description">Description</label>
                        <div id="editor">
                            <?php if (isset($_POST['description'])) {
                                echo GlobalUtility::unpackDescription($_POST['description']);
                            } else {
                                echo GlobalUtility::unpackDescription($project->description);
                            } ?>
                        </div>
                    </div>
                    <div class="form-group
                py-2">
                        <div class="custom-file-container" data-upload-id="my-unique-id"></div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-2">Update Project</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    const quill = new Quill('#editor', {
        theme: 'snow'
    });

    const form = document.querySelector('form');
    form.addEventListener('formdata', (event) => {
        // Append Quill content before submitting
        event.formData.append('description', JSON.stringify(quill.getContents().ops));

        // Append images before submitting
        const images = upload.cachedFileArray;
        images.forEach((image, index) => {
            event.formData.append(`image-${index}`, image);
        });
    });

    import { FileUploadWithPreview } from 'https://cdn.jsdelivr.net/npm/file-upload-with-preview@5.0.2/dist/file-upload-with-preview.esm.min.js'

    const upload = new FileUploadWithPreview('my-unique-id');
    upload.options.presetFiles = ["/<?= $project->img ?>"] // todo fix this presetimages
    upload.options.multiple = true;
</script>

