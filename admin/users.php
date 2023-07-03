<?php
if (!isset($_SESSION['access'])) {
    header('location: ../admin');
}
if ($_POST and isset($_GET['edit'])) {
    $admin = 0;
    if(isset($_POST['admin'])){
        $admin = 1;
    }
    $return = accounts::update($_GET['edit'], $_POST['password'], $_POST['username'], $admin);
    header('location: users');
}
if ($_POST and $_GET['add'] == 'account') {
    $admin = 0;
    if(isset($_POST['admin'])){
        $admin = 1;
    }
    $return = accounts::add($_POST['password'], $_POST['username'], $admin);
    echo $return;
}

if (isset($_GET["id"])) {
    accounts::sdelete($_GET["id"]);
    header('location: users');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <title>Admin - Users</title>
</head>

<body>
<?php
require_once 'header.php';

if (!isset($_SESSION['admin'])) {
    echo '<div class="welcome">
        <h1>Je hebt geen toegang</h1>
        
        </div>';
} elseif (isset($_GET['edit'])) {
    $result = accounts::loadaccount($_GET['edit']);
    $check = "";
    if ($result['admin']) {
        $check = "checked";
    }

    echo '<div class="admin">
        <form method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" value="' . $result['username'] . '">
        <label for="password">Password:</label>
        <input type="text" name="password" value="">
        <label for="admin">Admin:</label>
        <div class="toggle-pill-dark">
            <input type="checkbox" id="pill4" name="admin" ' . $check .'>
            <label for="pill4"></label>
        </div>
        <input type="submit" value="Versturen">
    </form></div>';
} elseif (isset($_GET['add']) == 'account') {
    echo '<div class="admin"><form method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <label for="pill4">Is admin:</label>
        <div class="toggle-pill-dark">
            <input type="checkbox" id="pill4" name="admin">
            <label for="pill4"></label>
        </div>
        <input type="submit" value="Versturen">
    </form></div>';
} else {
    $results = accounts::loadaccounts();
    echo "<div class='admin'><table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Admin</th>
                <th>Remove</th>
                <th>Edit</th>
            </tr>
        </thead><tbody>";

    foreach ($results as $result) {
        $admin = 'yes';
        if ($result['admin'] == 0) {
            $admin = 'no';
        }
        echo "
            <tr>
                <td>" . $result['username'] . "</td>
                <td class='ticon'>$admin</td>
                <td class='ticon'><a href='?id=" . $result['id'] . "' onclick='return confirm(\"weet je het zeker?\");'><i class='fa-solid fa-x'></i></a></td>
                <td class='ticon'><a href='?edit=" . $result['id'] . "'><i class='fa-solid fa-pen-to-square'></i></a></td>
            </tr>";
    }

    echo '</tbody></table></div>';
}
?>
</body>
<script src="../js/nav.js"></script>
</html>