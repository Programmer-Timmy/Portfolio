<?php
$env = parse_ini_file(__DIR__ . '/../../../../.env');

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

    if (isset($_POST['in_progress'])) {
        $_POST['in_progress'] = 1;
    } else {
        $_POST['in_progress'] = 0;
    }

    if (empty($error)) {
        $error = Projects::updateProject($_POST['title'], $_POST["description"], $_POST['link'], $_POST['github'], $_FILES, $_POST['pinned'], $_POST['in_progress'], $project->id);
        $error = Projects::updateProjectLanguages($_POST['project_languages'], $project->id);
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
<div class="container mx-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
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
                </div>
                <div class="row">
                    <div class="col form-group py-2">
                        <label for="link">Link</label>
                        <input type="text" class="form-control" id="link" name="link"
                               placeholder="Enter the link" <?php if (isset($_POST['link'])) {
                            echo 'value="' . $_POST['link'] . '"';
                        } else {
                            echo 'value="' . $project->path . '"';
                        } ?>>
                    </div>
                    <div class="col form-group py-2">
                        <label for="github">Github Link</label>
                        <input type="text" class="form-control" id="github" name="github"
                               placeholder="Enter the link" <?php if (isset($_POST['link'])) {
                            echo 'value="' . $_POST['github'] . '"';
                        } else {
                            echo 'value="' . $project->github . '"';
                        } ?>>
                        <div class="invalid-feedback">
                            Please enter a valid github link that is publicly accessible
                        </div>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" value="1" name="pinned"
                        <?= isset($_POST['pinned']) ? 'checked' : ($project->pinned ? 'checked' : '') ?>>
                    <label class="form-check-label" for="pinned">Pinned</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" value="1" name="in_progress"
                        <?= isset($_POST['in_progress']) ? 'checked' : ($project->in_progress ? 'checked' : '') ?>>
                    <label class="form-check-label" for="in_progress">Work In Progress</label>
                </div>

                <div class="form-group py-2">
                    <input type="hidden" name="project_languages" id="project_languages"
                           value='<?php if ($project->project_languages) {
                               $languagesArray = [];
                               foreach ($project->project_languages as $language) {
                                   $languagesArray[] = [
                                       'programming_languages_id' => $language->programming_languages_id,
                                       'percentage' => $language->percentage
                                   ];
                               }
                               echo json_encode($languagesArray);
                           } ?>'>
                    <div id="languages-container">
                        <?php if ($project->project_languages): ?>
                            <?php foreach ($project->project_languages as $language): ?>
                                <div class="card mb-2"
                                     style="background-color: <?= $language->color ?> !important; border-color: <?= $language->color ?> !important;">
                                    <div class="row card-body m-0 p-2 d-flex flex-row flex-wrap justify-content-between align-items-center">
                                        <div class="col-md-6">
                                            <select class="form-select" name="language" class="language-select">
                                                <?php foreach ($languages as $lang): ?>
                                                    <option value="<?= $lang->id ?>" <?php if ($lang->id == $language->programming_languages_id) {
                                                        echo 'selected';
                                                    } ?>><?= $lang->name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="number" class="form-control" name="percentage"
                                                   value="<?= $language->percentage * 1 ?>"
                                                   placeholder="Percentage" min="0" max="100" step="any">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger" onclick="removeLanguage(this)">
                                                X
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="addLanguage()">Add Language</button>
                </div>
                <div class="form-group py-2">
                    <label for="description">Description</label>
                    <div id="toolbar-container">
                        <span class="ql-formats">
                            <select class="ql-header"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-underline"></button>
                            <button class="ql-strike"></button>
                        </span>
                        <span class="ql-formats">
                            <select class="ql-color"></select>
                            <select class="ql-background"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-script" value="sub"></button>
                            <button class="ql-script" value="super"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-header" value="1"></button>
                            <button class="ql-header" value="2"></button>
                            <button class="ql-blockquote"></button>
                            <button class="ql-code-block"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                            <button class="ql-indent" value="-1"></button>
                            <button class="ql-indent" value="+1"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-direction" value="rtl"></button>
                            <select class="ql-align"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-link"></button>
                            <button class="ql-image"></button>
                            <button class="ql-video"></button>
                            <button class="ql-formula"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-clean"></button>
                        </span>
                    </div>
                    <div id="editor">
                    </div>
                </div>
                <div class="form-group py-2">
                    <div class="custom-file-container" data-upload-id="my-unique-id"></div>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-2">Update Project</button>
        </div>
        </form>
    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script type="module">

    const quill = new Quill('#editor', {
        modules: {
            toolbar: '#toolbar-container'
        },
        theme: 'snow'
    });

    quill.setContents(<?= $project->description ?>);

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

    import {
        FileUploadWithPreview
    } from 'https://cdn.jsdelivr.net/npm/file-upload-with-preview@5.0.2/dist/file-upload-with-preview.esm.min.js'

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
    const languagesData = <?= json_encode($project->project_languages); ?>;

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
