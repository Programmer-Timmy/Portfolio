<?php
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
    <title>remove</title>
</head>
<body>
    <?php
    foreach ($project as $project) {
        echo "<div><h1>$project->name</h1><a href='?id=$project->id' onclick='return confirm(\"weet je het zeker?\");'>X</a></div>";
    }
    ?>
</body>
</html>