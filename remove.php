<?php
session_start();

$con = new PDO("mysql:host=localhost;dbname=portfolio", "root", "");
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $con->prepare("SELECT * FROM projecten ORDER BY date DESC");
$stmt->execute();
$project = $stmt->fetchAll(pdo::FETCH_OBJ);


if (isset($_GET["id"])) {

    $stmt1 = $con->prepare("SELECT * FROM projecten WHERE id= ?");
    $stmt1->bindValue(1, $_GET["id"]);
    $stmt1->execute();
    $links = $stmt1->fetchAll(pdo::FETCH_OBJ);

    foreach ($links as $links) {
        $img = $links->img;
        $path = (substr($links->path, 0, -6));
        unlink($img);

        function deleteDirectory($path)
        {
            if (is_dir($path)) {
                $objects = scandir($path);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($path . DIRECTORY_SEPARATOR . $object) == "dir") {
                            deleteDirectory($path . DIRECTORY_SEPARATOR . $object);
                        } else {
                            unlink($path . DIRECTORY_SEPARATOR . $object);
                        }
                    }
                }
                reset($objects);
                rmdir($path);
            }
        }
        deleteDirectory($path);
    }

    $stmt2 = $con->prepare("DELETE FROM projecten WHERE id= ?");
    $stmt2->bindValue(1, $_GET["id"]);
    $stmt2->execute();

    header("location: /index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>remove</title>
</head>

<body>
    <?php
    if (!isset($_SESSION['loggedin'])) {
        header('location: account');
    } else {
        echo '<header><div class="container"><a href="javascript:void(0);" class="icon" onclick="MyFunction()">
        <i class="fa fa-bars"></i>
        </a>
        <nav id="nav">
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="add-project">Add project</a></li>
            <li><a href="remove">Remove</a></li>
        </ul>
        </nav></div></header>
        ';
        foreach ($project as $project) {
            echo "<div class='admin'><div><h1>$project->name<a href='?id=$project->id' onclick='return confirm(\"weet je het zeker?\");'>X</a></h1></div></div>";
        }
    }


    ?>
</body>
<script src="js/nav.js"></script>
</html>