<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("location:/");
}

require_once 'database.php';

global $con;

$stmt = $con->prepare("SELECT * FROM gebruiker WHERE id_gebruiker = ?");
$stmt->bindValue(1, $_SESSION['id']);
$stmt->execute();
$gebruiker = $stmt->fetchObject();


//saldo berekenen
//inkomsten ophalen
$stmt = $con->prepare("SELECT bedrag,idinkomen FROM inkomen WHERE id_gebruiker = ?");
$stmt->bindValue(1, $_SESSION['id']);
$stmt->execute();
$inkomen = $stmt->fetchAll(PDO::FETCH_OBJ);

$inkomstentotaal = 0;

// inkomsten optellen
foreach ($inkomen as $inkomst) {
    $inkomstentotaal = $inkomst->bedrag + $inkomstentotaal;
}


//Uitgaven ophalen
$stmt = $con->prepare("SELECT bedrag,id_uitgaven FROM uitgaven WHERE id_gebruiker = ?");
$stmt->bindValue(1, $_SESSION['id']);
$stmt->execute();
$uitgaven = $stmt->fetchAll(PDO::FETCH_OBJ);

$uitgaventotaal = 0;

// Uitgaven optellen
foreach ($uitgaven as $uitgave) {
    $uitgaventotaal = $uitgave->bedrag + $uitgaventotaal;
}


$saldo = $inkomstentotaal - $uitgaventotaal


?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">

</head>

<body>
<div class="header">
    <a href="#default" class="logo"> <?php
        echo("Hallo $gebruiker->voornaam $gebruiker->achternaam");

        ?>
    </a>
    <div class="header-right">
        <a class="active" href="portal.php">Home</a>
        <a href="inkomsten.php">Inkomsten</a>
        <a href="uitgaven.php">Uitgaven</a>
        <a href="schulden.php">Schulden</a>
        <a href="activa.php">Activa</a>
        <a href="logout.php">Uitloggen</a>
    </div>
</div>

<br><br>

<!-- saldo  -->
<H1 style="text-align: center;">SALDO</H1>
</p>

<h2 style="text-align: center;">$<?php echo $saldo; ?></h2>


</body>

</html>