<?php
include("requierd.php");

if ($_SESSION['access'] != "logged" or $_SESSION['access'] != 'admin' ) {
    header('location: account');
}
$projects = Projects::loadprojects("100");

if (isset($_GET["id"])) {
    projects::sdeleteproject($_GET["id"]);
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
        echo "<div class='admin'><div><h1>" . $project['name'] . "<a href='?id=". $project['id'] . "' onclick='return confirm(\"weet je het zeker?\");'>X</a></h1></div></div>";
    }
    ?>
</body>
<script src="js/nav.js"></script>

</html>