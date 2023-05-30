<?php
session_start();
require 'database.php';
if (!isset($_SESSION['id'])) {
    header("location: index.php");
}

if (isset($_GET["id"])) {
    $stmt = $con->prepare("DELETE FROM uitgaven WHERE id_uitgaven = ?");
    $stmt->bindValue(1, $_GET["id"]);

    $stmt->execute();
    header("location: uitgaven.php");
}

$stmt = $con->prepare("SELECT * FROM gebruiker WHERE id_gebruiker = ?");
$stmt->bindValue(1, $_SESSION['id']);
$stmt->execute();
$gebruiker = $stmt->fetchObject();

$stmt = $con->prepare(
    "SELECT uitgaven.id_uitgaven, uitgaven.bedrag, uitgaven.datum, uitgaven.vaste_uitgaven, uitgaven.id_gebruiker, uitgaven_soort.soort
FROM uitgaven
JOIN uitgaven_soort ON uitgaven.id_uitgaven_soort = uitgaven_soort.id_uitgaven_soort
WHERE id_gebruiker = ?"
);
$stmt->bindValue(1, $_SESSION['id']);
$stmt->execute();

$tests = $stmt->fetchAll(PDO::FETCH_OBJ);


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

        ?></a>
    <div class="header-right">
        <a href="portal.php">Home</a>
        <a href="inkomsten.php">Inkomsten</a>
        <a class="active" href="uitgaven.php">Uitgaven</a>
        <a href="schulden.php">Schulden</a>
        <a href="activa.php">Activa</a>
        <a href="logout.php">Uitloggen</a>
    </div>
</div>

<br>


<h1>Uitgaven <a href="transactie.php" class="btn btn-primary">+</a></h1>
<table class='table table-striped'>
    <thead class='table-dark>'>
    <th>Bedrag</th>
    <th>Datum</th>
    <th>Soort</th>
    <th>Periodiek</th>
    <th></th>
    </thead>
    <tbody>
    <?php
    foreach ($tests as $test) {
        $newDate = date("d-m-Y", strtotime($test->datum));
        echo "<tr>";
        echo "<td>€" . str_replace('.', ',', $test->bedrag) . "</td>";
        echo "<td>$newDate</td>";
        echo "<td>$test->soort</td>";

        if ($test->vaste_uitgaven == 1) {
            echo "<td>Ja</td>";
        } else {
            echo "<td>Nee</td>";
        }

        echo "<td><a class='btn btn-danger' href='uitgaven.php?id=$test->id_uitgaven' onclick='return confirm(\"Weet je het zeker?\");'>X</a></td>";

        echo "</tr>";
    }
    ?>
    </tbody>
</table>


</body>

</html>