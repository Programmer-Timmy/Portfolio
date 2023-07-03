<?php
if (!isset($_SESSION['access'])) {
    header('location: ../admin');
}

if ($_POST) {
    $test = projects::addproject($_FILES, $_POST["name"], $_POST["git"], $_POST["link"]);
    echo $test;
}
?>

<!DOCTYPE html>
<html lang="NL">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <title>Admin - Add project</title>
</head>

<body>
<?php require_once 'header.php'; ?>
<div class="admin">
    <form method="post" enctype="multipart/form-data">

        <label for="name">Naam van het project:</label><input type="text" name="name" id="name" required>

        <label for="git">Github link:</label><input type="text" name="git" id="git">
        <container>
            <label for="img" class="drop-container">
                <span class="drop-title">Drop image here</span>
                or
                <input type="file" name="img" id="img" accept="image/png, image/jpeg" required>
            </label>
            <label for="zip_file" class="drop-container">
                <span class="drop-title">Drop Project here</span>
                or
                <input type="file" name="zip_file" id="zip_file" accept=".zip,.rar,.7zip">
                or link
                <input type="text" name="link" id="link">
            </label>
        </container>
        <label
                for="link"></label>
        <input type="submit" value="Toevoegen" name="submit">
    </form>
</div>
</body>
<script src="../js/nav.js"></script>
</html>