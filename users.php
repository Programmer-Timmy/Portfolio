<?php
include('requierd.php');
if ($_SESSION['access'] != "logged") {
    header('location: account');
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
    <title>Account</title>
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
    if ($_SESSION['admin'] != true) {
        echo '<div class="welcome">
        <h1>Je hebt geen toegang</h1>
        
        </div>';
    } else {
        echo '
        <div class="welcome">
        <h1>Welcome!</h1>
        <h2>You are logged in!</h2>
        </div>';
    }
    ?>

</body>
<script src="js/nav.js"></script>

</html>