<?php
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $data = AuthControler::login($username, $password);
    if ($data != null) {
        header('Location: ' . $data);
    } else {
        $_SESSION['error'] = 'Invalid username or password';
    }
}
?>
<main class="container mt-5">
    <section class="row justify-content-center">
        <div class="col-md-6">
            <header class="text-center mb-4">
                <h1 class="h3 title">Login</h1>
            </header>
            <form method="post" style="color: #777">
                <?php GlobalUtility::displayFlashMessages(); ?>
                <div class="form-group py-2">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                           placeholder="Enter your username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Enter your password">
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-2">Login</button>
            </form>
        </div>
    </section>
</main>
