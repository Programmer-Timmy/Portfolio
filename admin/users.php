<?php
if (!isset($_SESSION['access'])) {
    header('location: ../admin');
}
if ($_POST and isset($_GET['edit'])) {
    $return = accounts::update($_GET['edit'], $_POST['password'], $_POST['username'], $_POST['admin']);
    header('location: users');
}
if ($_POST and $_GET['add'] == 'account') {
    $return = accounts::add($_POST['password'], $_POST['username'], $_POST['admin']);
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
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <title>Users</title>
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
        <input type="text" name="username" value="' . $result['username'] . '"><br>
        <label for="password">Password:</label>
        <input type="text" name="password" value=""><br>
        <label for="admin">Admin:</label>
        <input type="hidden" value="0" name="admin">
        <input type="checkbox" name="admin" id="admin" value="1" class="checkbox"' . $check . '><br>
        <input type="submit" value="Versturen">
    </form></div>';
    } elseif (isset($_GET['add']) == 'account') {
        echo '<div class="admin"><form method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br>
        <label for="admin">Admin:</label>
        <input type="checkbox" name="admin" id="admin" value="1" class="checkbox"><br>
        <input type="submit" value="Versturen">
    </form></div>';
    } else {
        $results = accounts::loadaccounts();
        echo "<div class='admin'><table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Remove</th>
                <th>Edit</th>
            </tr>
        </thead><tbody>";

        foreach ($results as $result) {
            echo "
            <tr>
                <td>" . $result['username'] . "</td>
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