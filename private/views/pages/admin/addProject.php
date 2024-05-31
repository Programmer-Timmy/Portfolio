<?php
$error = '';
if ($_POST) {
    $descriptionArray = json_decode($_POST['description']);
    if (sizeof($_FILES) < 1) {
        $error = 'Please upload an image';
    }
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
        $error = Projects::addProject($_POST['title'], $_POST["description"], $_POST['github'], $_POST['link'], $_FILES, $_POST['pinned']);
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

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="welcome text-center mb-4">
                <h3> Add Project</h3>
            </div>
            <form method="post" style="color: #777" enctype="multipart/form-data">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                <div class="form-group
                py-2">
                    <label for="title">Title</label>
                    <input type="text" class="form-control"
                           id="title" <?php if (isset($_POST['title'])) echo 'value="' . $_POST['title'] . '"' ?>
                           required name="title" placeholder="Enter the title">
                </div>
                <div class="form-group
                py-2">
                    <label for="link">Link</label>
                    <input type="text" class="form-control" id="link" name="link"
                           placeholder="Enter the link" <?php if (isset($_POST['link'])) echo 'value="' . $_POST['link'] . '"' ?>>
                </div>
                <div class="form-group py-2">
                    <label for="github">Github Link</label>
                    <input type="text" class="form-control" id="github" name="github"
                           placeholder="Enter the link" <?php if (isset($_POST['link'])) {
                        echo 'value="' . $_POST['github'] . '"';
                    } ?>>
                </div>
                <div class="form-check py-2">
                    <input type="checkbox" class="form-check-input" id="pinned" name="pinned"
                           placeholder="Enter the link" <?php if (isset($_POST['pinned'])) {
                        echo 'value="' . $_POST['pinned'] . '"';
                    } ?>>
                    <label for="form-check-label">Pinned</label>

                </div>
                <div class="form-group
                py-2">
                    <label for="description">Description</label>
                    <div id="editor">
                        <?php if (isset($_POST['description'])) : ?>
                            <?= GlobalUtility::unpackDescription($_POST['description']) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group
                py-2">
                    <div class="custom-file-container" data-upload-id="my-unique-id" multiple></div>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-2">Add Project</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script src="https://unpkg.com/file-upload-with-preview/dist/file-upload-with-preview.iife.js"></script>
<script>
    const quill = new Quill('#editor', {
        theme: 'snow'
    });

    const upload = new FileUploadWithPreview.FileUploadWithPreview('my-unique-id');
    upload.options.multiple = true;

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

</script>

