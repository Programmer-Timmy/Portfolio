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
$existingProjectImages = [$project->img];
$additionalProjectImages = Projects::loadProjectImg($project->id);
if ($additionalProjectImages) {
    foreach ($additionalProjectImages as $image) {
        $existingProjectImages[] = $image->img;
    }
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

    $_POST['private_repo'] = empty($_POST['private_repo']) ? Null : ($_POST['private_repo'] == 'true' ? 1 : 0);


    if (empty($error)) {
        $error = Projects::updateProject($_POST['title'], $_POST["description"], $_POST['link'], $_POST['github'], $_FILES, $_POST['pinned'], $_POST['in_progress'], $project->id, $_POST['private_repo'], $_POST['image_state'] ?? null);
        if (empty($error)) {
            $error = Projects::updateProjectLanguages($_POST['project_languages'], $project->id);
        }
        if (empty($error)) {
            $error = Projects::updateProjectContributors($_POST['contributors'], $project->id);
        }
        if (empty($error)) {
            header('Location: /admin/projects');
        }
    }
}

$languages = Database::getAll('programming_languages', ['id', 'name', 'color'], [], [], 'name ASC');
$initialImageState = json_encode(['images' => $existingProjectImages, 'removed' => []]);

?>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet"/>
<link
        rel="stylesheet"
        type="text/css"
        href="https://unpkg.com/file-upload-with-preview/dist/style.css"
/>
<style>
    .image-manager-card.is-removed {
        opacity: 0.55;
    }

    .image-manager-thumb {
        width: 100%;
        max-height: 150px;
        object-fit: cover;
        border-radius: 0.375rem;
    }

    .image-manager-status {
        font-size: 0.875rem;
        font-weight: 600;
    }
</style>
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

                    <button type="button" class="btn btn-primary" id="add-language-button" onclick="addLanguage()">Add Language</button>
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
                    <label class="mb-2">Manage existing images</label>
                    <input type="hidden" name="image_state" id="image_state"
                           value="<?= htmlspecialchars(isset($_POST['image_state']) ? $_POST['image_state'] : $initialImageState, ENT_QUOTES, 'UTF-8') ?>">
                    <div id="image-manager" class="row g-2">
                        <?php foreach ($existingProjectImages as $index => $image): ?>
                            <div class="col-md-6 image-manager-card" data-image-path="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>">
                                <div class="card h-100">
                                    <div class="card-body p-2">
                                        <img src="/<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
                                             alt="Project image <?= $index + 1 ?>"
                                             class="image-manager-thumb mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="image-manager-status"></span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-action="set-cover">Set cover</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-action="move-up">Up</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-action="move-down">Down</button>
                                            <button type="button" class="btn btn-sm btn-danger" data-action="toggle-remove">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group py-2">
                    <label class="mb-2">Upload new images (optional)</label>
                    <div class="custom-file-container" data-upload-id="my-unique-id"></div>
                </div>
                <input type="hidden" id="private_repo" name="private_repo" value="">
                <input type="hidden" id="contributors" name="contributors" value="[]">
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

    // interval
    setInterval(() => {
        $('#file-upload-with-preview-my-unique-id').attr('multiple', 'multiple');
    }, 1000);


</script>


<script>
    const addLanguageButton = document.getElementById('add-language-button'); // Reference to the add button
    const languagesContainer = document.getElementById('project_languages'); // Reference to the languages container
    // validate of the github link is a valid github link\
    const githubInput = $('#github');
    const languages = <?= json_encode($languages) ?>;

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
            showLanguageButtons(); // Show buttons if the format is incorrect
        }
        const githubRepoRegex = /^https:\/\/github\.com\/[^\/]+\/[^\/]+$/;
        if (!githubRepoRegex.test(githubLink)) {
            githubInput.removeClass('is-valid');
            githubInput.addClass('is-invalid');
            showLanguageButtons(); // Show buttons if the format is incorrect
            setPrivateRepo(); // Call function to set private repo
            return;
        }

        const valid = getGithubRepo(githubLink);
        valid.then((data) => {
            console.log(data);
            if (data) {
                githubInput.removeClass('is-invalid');
                githubInput.addClass('is-valid');
                setPrivateRepo(data.private); // Call function to set private repo
                hideLanguageButtons(); // Hide buttons if the repo is valid
                fetchLanguages(githubLink); // Fetch languages if the repo is valid
                fetchContributors(githubLink); // Fetch contributors if the repo is valid
            } else {
                githubInput.removeClass('is-valid');
                githubInput.addClass('is-invalid');
                removeLanguages(); // Remove languages if the repo is invalid
                showLanguageButtons(); // Show buttons if the format is incorrect
                setPrivateRepo(); // Call function to set private repo
                updateContributors([]); // Call function to update contributors
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
                    'User-Agent': 'programmer-timmy',
                    'Authorization': 'token <?= $env['GITHUB_TOKEN'] ?>'
                }
            });

            if (response.ok) {
                const data = await response.json();
                return data;
            } else {
                return false;
            }
        } catch (error) {
            return false;
        }
    }

    async function fetchLanguages(githubLink) {
        // Split the URL to get user and repo
        githubLink = githubLink.split('/');
        const repo = githubLink[githubLink.length - 1];
        const user = githubLink[githubLink.length - 2];
        const url = `https://api.github.com/repos/${user}/${repo}/languages`;

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'User-Agent': 'programmer-timmy',
                    'Authorization': 'token <?= $env['GITHUB_TOKEN'] ?>'
                }
            });

            if (response.ok) {
                const languagesData = await response.json();
                fillLanguages(languagesData); // Call function to fill language data
            }
        } catch (error) {
            console.error('Error fetching languages:', error);
        }
    }

    async function fetchContributors(githubLink) {
        // Split the URL to get user and repo
        githubLink = githubLink.split('/');
        const repo = githubLink[githubLink.length - 1];
        const user = githubLink[githubLink.length - 2];
        const url = `https://api.github.com/repos/${user}/${repo}/contributors`;

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'User-Agent': 'programmer-timmy',
                    'Authorization': 'token <?= $env['GITHUB_TOKEN'] ?>'
                }
            });

            if (response.ok) {
                const contributorsData = await response.json();
                if (contributorsData.length > 0) {
                    updateContributors(contributorsData); // Call function to update contributors
                }
            }
        } catch (error) {
            console.error('Error fetching contributors:', error);
        }

    }

    function setPrivateRepo(private) {
        const privateRepo = document.getElementById('private_repo');
        privateRepo.value = private;
    }

    function fillLanguages(data) {
        const languagesContainer = document.getElementById('languages-container');
        console.log(data);

        // Clear existing language cards before filling new data
        languagesContainer.innerHTML = '';
        //1370
        const totalAmount = Object.values(data).reduce((a, b) => a + b, 0);
        let otherAmount = 0;

        // Iterate over the language data and create cards
        for (const [language, percentage] of Object.entries(data)) {
            ;
            const languageId = languages.find(lang => lang.name === language).id;
            const languageColor = languages.find(lang => lang.name === language).color;
            const percentageValue = ((percentage / totalAmount) * 100).toFixed(1);

            if (percentageValue < 1) {
                otherAmount += percentage;
                continue;
            }
            // Create a new card for each language
            const card = document.createElement('div');
            card.classList.add('card', 'mb-2');
            card.setAttribute('style', `background-color: ${languageColor}; border-color: ${languageColor}`);
            card.innerHTML = `
                <div class="row card-body m-0 p-2 d-flex flex-row flex-wrap justify-content-between align-items-center">
                    <div class="col-md-6">
                        <select class="form-select disabled" disabled name="language">
                            <option value="${languageId}" selected disabled>${language}</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" disabled class="form-control" name="percentage" placeholder="Percentage" min="0" max="100" step="any" value="${percentageValue * 1}">
                    </div>
                </div>
            `;
            languagesContainer.appendChild(card);
        }
        if (otherAmount > 0) {
            const languageId = languages.find(lang => lang.name === 'Other').id;
            const percentageValue = ((otherAmount / totalAmount) * 100).toFixed(1);
            const card = document.createElement('div');
            card.classList.add('card', 'mb-2');
            card.innerHTML = `
                <div class="row card-body m-0 p-2 d-flex flex-row flex-wrap justify-content-between align-items-center">
                    <div class="col-md-6">
                        <select class="form-select disabled" disabled name="language">
                            <option value="${languageId}" selected disabled>Other</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" disabled class="form-control" name="percentage" placeholder="Percentage" min="0" max="100" step="any" value="${percentageValue * 1}">
                    </div>
                </div>
            `;
            languagesContainer.appendChild(card);
        }


        // Update hidden input with the new languages
        updateHiddenInput();
    }



    function hideLanguageButtons() {
        addLanguageButton.style.display = 'none'; // Hide the Add button
        const deleteButtons = languagesContainer.querySelectorAll('.btn-danger');
        deleteButtons.forEach(button => button.style.display = 'none'); // Hide all Delete buttons
    }

    function showLanguageButtons() {
        addLanguageButton.style.display = 'block'; // Show the Add button
        const deleteButtons = languagesContainer.querySelectorAll('.btn-danger');
        deleteButtons.forEach(button => button.style.display = 'block'); // Show all Delete buttons
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

    function removeLanguages() {
        const languagesContainer = document.getElementById('languages-container');
        languagesContainer.innerHTML = '';
        updateHiddenInput();
    }

    function updateContributors(contributers) {
        const contributorsArray = [];

        for (const contributor of contributers) {
            contributorsArray.push({
                user: {
                    id: contributor.id,
                    login: contributor.login,
                    avatar_url: contributor.avatar_url,
                    html_url: contributor.html_url
                },
                contributions: contributor.contributions
            });
        };

        contributors.value = JSON.stringify(contributorsArray);

        console.log(contributorsArray);
    }

    // Add event listeners to update hidden input when percentage or language changes
    document.getElementById('languages-container').addEventListener('change', updateHiddenInput);

    const imageManager = document.getElementById('image-manager');
    const imageStateInput = document.getElementById('image_state');

    function getImageCards() {
        return Array.from(imageManager.querySelectorAll('.image-manager-card'));
    }

    function syncImageState() {
        const cards = getImageCards();
        const images = cards.map(card => card.dataset.imagePath);
        const removed = cards
            .filter(card => card.classList.contains('is-removed'))
            .map(card => card.dataset.imagePath);

        imageStateInput.value = JSON.stringify({
            images: images,
            removed: removed
        });
    }

    function refreshImageLabels() {
        const cards = getImageCards();
        const activeCards = cards.filter(card => !card.classList.contains('is-removed'));

        cards.forEach(card => {
            const status = card.querySelector('.image-manager-status');
            const removeButton = card.querySelector('[data-action="toggle-remove"]');
            const isRemoved = card.classList.contains('is-removed');

            if (isRemoved) {
                status.textContent = 'Removed';
                removeButton.textContent = 'Undo';
                removeButton.classList.remove('btn-danger');
                removeButton.classList.add('btn-warning');
                return;
            }

            const activeIndex = activeCards.indexOf(card);
            if (activeIndex === 0) {
                status.textContent = 'Cover image';
            } else {
                status.textContent = `Gallery image ${activeIndex}`;
            }

            removeButton.textContent = 'Remove';
            removeButton.classList.remove('btn-warning');
            removeButton.classList.add('btn-danger');
        });
    }

    function applyImageStateFromInput() {
        if (!imageStateInput.value) {
            return;
        }

        let state;
        try {
            state = JSON.parse(imageStateInput.value);
        } catch (error) {
            return;
        }

        if (!state || !Array.isArray(state.images)) {
            return;
        }

        const cardByImage = new Map();
        getImageCards().forEach(card => {
            cardByImage.set(card.dataset.imagePath, card);
        });

        state.images.forEach(imagePath => {
            const card = cardByImage.get(imagePath);
            if (!card) {
                return;
            }
            imageManager.appendChild(card);
        });

        if (Array.isArray(state.removed)) {
            const removedLookup = new Set(state.removed);
            getImageCards().forEach(card => {
                card.classList.toggle('is-removed', removedLookup.has(card.dataset.imagePath));
            });
        }
    }

    imageManager.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const card = button.closest('.image-manager-card');
        if (!card) {
            return;
        }

        const action = button.dataset.action;
        if (action === 'toggle-remove') {
            card.classList.toggle('is-removed');
        }

        if (!card.classList.contains('is-removed')) {
            if (action === 'set-cover') {
                const firstActive = getImageCards().find(currentCard => !currentCard.classList.contains('is-removed'));
                if (firstActive) {
                    imageManager.insertBefore(card, firstActive);
                } else {
                    imageManager.prepend(card);
                }
            } else if (action === 'move-up') {
                const previousCard = card.previousElementSibling;
                if (previousCard) {
                    imageManager.insertBefore(card, previousCard);
                }
            } else if (action === 'move-down') {
                const nextCard = card.nextElementSibling;
                if (nextCard) {
                    imageManager.insertBefore(nextCard, card);
                }
            }
        }

        refreshImageLabels();
        syncImageState();
    });

    applyImageStateFromInput();
    refreshImageLabels();
    syncImageState();
</script>

