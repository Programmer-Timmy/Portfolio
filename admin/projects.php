<?php
require('../requierd.php');
if (!isset($_SESSION['access'])) {
    header('location: ../admin');
}
if ($_POST and isset($_GET['edit'])) {
    $return = Projects::update($_GET['edit'], $_POST['name'], $_POST['github']);
    echo $return;
    header('location: projects');
}

if (isset($_GET["id"])) {
    projects::sdeleteproject($_GET["id"]);
    header('location: projects');
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
    <title>Projects</title>
</head>

<body>
    <?php
    require_once 'header.php';
    if (isset($_GET['edit'])) {
        $result = Projects::loadproject($_GET['edit']);

        echo '<div class="admin">
        <form method="post" enctype="multipart/form-data">
            Naam van het project:
            <input type="text" name="name" id="name" value="' . $result['name'] . '" required><br>
            Github link:
            <input type="text" name="github" id="git" value="' . $result['github'] . '"><br>
            <input type="submit" value="Update" name="submit">
            </div>
        </form>';
    } else {
        $results = Projects::loadprojects(100);
        echo "<div class='admin'><table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Remove</th>
                <th>Edit</th>
            </tr>
        </thead>";

        foreach ($results as $result) {
            echo "<tbody>
            <tr>
                <td>" . $result['name'] . "</td>
                <td class='ticon'><a href='?id=" . $result['id'] . "' onclick='return confirm(\"weet je het zeker?\");'><i class='fa-solid fa-x'></i></a></td>
                <td class='ticon'><a href='?edit=" . $result['id'] . "'><i class='fa-solid fa-pen-to-square'></i></a></td>
            </tr>
            </tbody>";
        }

        echo '</table></div>';
    }
    ?>
</body>
<script src="../js/nav.js"></script>

</html>