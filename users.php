<?php
include('requierd.php');
if ($_SESSION['access'] != "logged") {
    header('location: account');
}
if($_POST){
    $result = accounts::loadaccount($_GET['edit']);
    $password = $_POST['password'];
    if($_POST["password"] == ""){
        $password = $result['password'];
    };
    

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
    } elseif (isset($_GET['edit'])){
        $result = accounts::loadaccount($_GET['edit']);
        $check = "";
        if ($result['admin'] == true) {
            $check = "checked";
        }

        echo '<form method="post">
        <input type="text" name="username" value="'.$result['username'] .'">
        <input type="text" name="password" value="">
        <input type="hidden" value="0" name="admin">
        <input type="checkbox" name="admin" id="admin" value="1"'. $check. '>
        <input type="submit" value="versturen">
    </form>';
    } else {
        $results = accounts::loadaccounts();
        foreach ($results as $result) {
            echo "<div class='admin'><div><h1>" . $result['username'] . "<a href='?id=" . $result['id'] . "' onclick='return confirm(\"weet je het zeker?\");'>X</a><a href='?edit=" . $result["id"] . "'>edit</a></h1></div></div>";
        }
    }
    ?>
</body>
<script src="js/nav.js"></script>

</html>