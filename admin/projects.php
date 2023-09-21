<?php

Projects::delete();

$result = Projects::loadproject($_GET['edit']);

if (!isset($_SESSION['access'])) {
    header('location: ../admin');
}
if ($_POST and isset($_GET['edit'])) {
    Projects::update($_GET['edit'], $_POST['name'], $_POST['git'], $result['img'], $result['path'], $_FILES, htmlentities($_POST['description']), $_POST['password'], $_POST['username']);
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
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <script src="https://kit.fontawesome.com/65416f0144.js" crossorigin="anonymous"></script>
    <title>Admin - Projects</title>
</head>

<body>
<?php
require_once 'header.php';
if (isset($_GET['edit'])) {

    echo "<div class=\"admin\">
        <form method=\"post\" enctype=\"multipart/form-data\">
            <row>
                <label for=\"name\">Name of the project:<input type=\"text\" name=\"name\" id=\"name\" value=\"" . $result['name'] . "\"required></label>     
                <label for=\"git\">Github link:<input type=\"text\" name=\"git\" value=\"" . $result['github'] . "\" id=\"git\"></label>
            </row>
            <row>
                <label for=\"username\">Guest username:<input type=\"text\" name=\"username\" id=\"username\" value=\"" . $result['guest_username'] . "\"></label>
                <label for=\"password\">Guest password:<input type=\"text\" name=\"password\" id=\"password\" value=\"" . $result['guest_password'] . "\"></label>
        </row>
            <label for=\"description\" id=\"description\">Description: <textarea name=\"description\">" . $result['description'] . "</textarea> </label>
            <container>
                <label for=\"img\" class=\"drop-container\" style=\"background-image:url('http://portfolio.timmygamer.nl/" . $result['img'] . "');justify-content: flex-end; background-size: 100%; \">
                <input type=\"file\" name=\"img\" id=\"img\" accept=\"image/png, image/jpeg\">

            </label>
            <label for=\"zip_file\" class=\"drop-container\">
                <span class=\"drop-title\">Change the project</span>
                or
                <input type=\"file\" name=\"zip_file\" id=\"zip_file\" accept=\".zip,.rar,.7zip\">
                or link
                <input type=\"text\" name=\"link\" value='" . $result['path'] . "'id=\"link\">
            </label>
        </container>
        <label
                for=\"link\"></label>
        <input type=\"submit\" value=\"Change\" name=\"submit\">
    </form>
</div>";
} else {
    $results = Projects::loadprojects(100);
    echo "<div class='admin'><table>
        <thead>
            <tr>
                <th>Name</th>
                <th>GitHub</th>
                <th>Remove</th>
                <th>Edit</th>
            </tr>
        </thead><tbody>";

    foreach ($results as $result) {
        $git = 'yes';
        if (!$result['github']) {
            $git = 'no';
        }

        echo "
            <tr>
                <td>" . $result['name'] . "</td>
                <td class='ticon'>$git</td>
                <td class='ticon'><a href='?id=" . $result['id'] . "' onclick='return confirm(\"weet je het zeker?\");'><i class='fa-solid fa-x'></i></a></td>
                <td class='ticon'><a href='?edit=" . $result['id'] . "'><i class='fa-solid fa-pen-to-square'></i></a></td>
            </tr>
            ";
    }
    echo '</tbody></table></div>';
}
?>
</body>
<script src="../js/nav.js"></script>
</html>