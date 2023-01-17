<?php
include("requierd.php");

if ($_SESSION['access'] != "logged") {
    header('location: account');
}

$projects = $database->getRows('projecten', FALSE , FALSE, FALSE, 'date DESC LIMIT 3');

if (isset($_GET["id"])) {
    $account = $database::getRow('projecten', ['id'], 's', [$_POST['id']]);           
    $path = (substr($account['path'], 0, -6));
    unlink($account['img']);

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
    // ! needs to be changed!
    $account = $db->query('DELETE FROM projecten WHERE id= ?', array($_GET["id"]));
    header('location: /remove');
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
    <header>
        <div class="container"><a href="javascript:void(0);" class="icon" onclick="MyFunction()">
                <i class="fa fa-bars"></i>
            </a>
            <nav id="nav">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="add-project">Add project</a></li>
                    <li><a href="remove">Remove</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <?php
    foreach ($projects as $project) {
        echo "<div class='admin'><div><h1>" . $project['name'] . "<a href='?id= ".$project['id'] . "' onclick='return confirm(\"weet je het zeker?\");'>X</a></h1></div></div>";
    }
    ?>
</body>
<script src="js/nav.js"></script>

</html>