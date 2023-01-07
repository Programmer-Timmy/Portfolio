<?php
$con = new PDO("mysql:host=localhost;dbname=portfolio", "root", "");
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $con->prepare("SELECT * FROM projecten ORDER BY date DESC");
$stmt->execute();
$project = $stmt->fetchAll(pdo::FETCH_OBJ);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Projects</title>

</head>

<body>
    <header>
        <div class="container">

            <h1 class="logo">Tim van der Kloet</h1>
            <a href="javascript:void(0);" class="icon" onclick="MyFunction()">
                <i class="fa fa-bars"></i>
            </a>
            <nav id="nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="Contact.php">Contact</a></li>
                    <li><a href="">Projects</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="welcome">
        <h1>Projects</h1>
    </div>



    <div class="borderp">
        <?php
        foreach ($project as $project) {

            echo "
            <div class='project-home'>
                <a href='$project->path'><img src='$project->img' class='img-size' alt=''></a>
                <h1>$project->name</h1>
            </div>";
        }
        ?>
    </div>

</body>
<script src="js/nav.js"></script>

</html>