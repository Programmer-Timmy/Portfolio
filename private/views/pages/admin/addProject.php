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
        $error = Projects::addProject($_POST['title'], $_POST["description"], $_POST['link'], $_POST['github'], $_FILES, $_POST['pinned']);
        $error = Projects::addProjectLanguages(json_decode($_POST['project_languages']), $error);
        if (empty($error)) {
            header('Location: /admin/projects');
        }
    }
}

$languages = Database::getAll('programming_languages', ['id', 'name', 'color'], [], [], 'name ASC');

?>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet"/>
<link
        rel="stylesheet"
        type="text/css"
        href="https://unpkg.com/file-upload-with-preview/dist/style.css"
/>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
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
                    <div class="invalid-feedback">
                        Please enter a valid github link that is publicly accessible
                    </div>
                </div>
                <div class="form-group py-2">
                    <input type="hidden" name="project_languages" id="project_languages" value="[]">
                    <div id="languages-container">
                    </div>
                    <button type="button" class="btn btn-primary" onclick="addLanguage()">Add Language</button>
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
<script type="module">

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
    upload.options.multiple = true;
</script>
<script>
    // validate of the github link is a valid github link\
    const githubInput = $('#github');

    githubInput.on('input', function () {

        const githubLink = $(this).val();
        checkIfRepoExists(githubLink);
    });

    checkIfRepoExists(githubInput.val());

    function checkIfRepoExists(githubLink) {
        if (!githubLink) {
            githubInput.removeClass('is-valid');
            githubInput.removeClass('is-invalid');
            return;
        }
        const githubRepoRegex = /^https:\/\/github\.com\/[^\/]+\/[^\/]+$/;
        if (!githubRepoRegex.test(githubLink)) {
            githubInput.removeClass('is-valid');
            githubInput.addClass('is-invalid');
            return;
        }

        const valid = getGithubRepo(githubLink);
        valid.then((data) => {
            if (data) {
                githubInput.removeClass('is-invalid');
                githubInput.addClass('is-valid');
            } else {
                githubInput.removeClass('is-valid');
                githubInput.addClass('is-invalid');
            }
        });
    }

    async function getGithubRepo(githubLink) {
        // Split the URL to get user and repo
        githubLink = githubLink.split('/');
        const repo = githubLink[githubLink.length - 1];
        const user = githubLink[githubLink.length - 2];
        const url = `https://api.github.com/repos/${user}/${repo}`;

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'User-Agent': 'programmer-timmy'
                }
            });

            if (response.ok) {
                console.log(response);
                const data = await response.json();
                return data;
            } else {
                return false;
            }
        } catch (error) {
            return false;
        }
    }
</script>
<script>
    // Initialize the languages array with PHP data
    function updateHiddenInput() {
        const languagesContainer = document.getElementById('languages-container');
        const languageCards = languagesContainer.querySelectorAll('.card');
        const languagesArray = [];

        languageCards.forEach(card => {
            const languageSelect = card.querySelector('select[name="language"]');
            const percentageInput = card.querySelector('input[name="percentage"]');
            const languageId = languageSelect.value;
            const percentage = percentageInput.value;

            if (languageId && percentage) {
                languagesArray.push({
                    programming_languages_id: languageId,
                    percentage: percentage
                });
            }
        });

        console.log(languagesArray);

        // Update hidden input with the JSON string
        document.getElementById('project_languages').value = JSON.stringify(languagesArray);
    }

    function addLanguage() {
        const languagesContainer = document.getElementById('languages-container');

        // Create a new card for the language
        const card = document.createElement('div');
        card.classList.add('card', 'mb-2');
        card.innerHTML = `
            <div class="row card-body m-0 p-2 d-flex flex-row flex-wrap justify-content-between align-items-center">
                <div class="col-md-6">
                    <select class="form-select" name="language">
                        <?php foreach ($languages as $lang): ?>
                            <option value="<?= $lang->id ?>"><?= $lang->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control" name="percentage" placeholder="Percentage" min="0" max="100" step="any">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger" onclick="removeLanguage(this)">X</button>
                </div>
            </div>
        `;

        languagesContainer.appendChild(card);

        // Update the hidden input with the new languages
        updateHiddenInput();
    }

    function removeLanguage(button) {
        // Remove the card containing the clicked button
        button.closest('.card').remove();

        // Update the hidden input with the new languages
        updateHiddenInput();
    }

    // Add event listeners to update hidden input when percentage or language changes
    document.getElementById('languages-container').addEventListener('change', updateHiddenInput);
</script>
