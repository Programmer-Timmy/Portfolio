<?php
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'];
    $username = $_POST['username'];
    
    if (empty($url) || empty($username)) {
        $error = "Please fill in all fields.";
    } else {
        $result = OpenSource::addProject($url, $username);
        if (strpos($result, "Error") === 0) {
            $error = $result;
        } else {
            // Success
            header('Location: /admin/opensource');
            exit;
        }
    }
}
?>
<div class="container">
    <div class="welcome text-center mb-4">
        <h1>Add Open Source Project</h1>
        <p>Enter the GitHub repository URL and your username to automatically fetch your Pull Requests.</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="w-50 mx-auto">
        <div class="mb-3">
            <label for="url" class="form-label">GitHub Repository URL</label>
            <input type="url" class="form-control" id="url" name="url" placeholder="https://github.com/owner/repo" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Your GitHub Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="e.g. timvanderkloet" required>
            <div class="form-text">We'll verify you are the author of the PRs.</div>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Fetch & Add Project</button>
            <a href="/admin/opensource" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
