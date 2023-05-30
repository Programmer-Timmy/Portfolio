<?php
session_start();
require 'database.php';
if (!isset($_SESSION['id'])) {
    header("location: index.php");
}

if (isset($_GET["id"])) {
    $stmt = $con->prepare("DELETE FROM activa WHERE id_activa = ?");
    $stmt->bindValue(1, $_GET["id"]);

    $stmt->execute();
    header("location: activa.php");
}

$stmt = $con->prepare("SELECT * FROM gebruiker WHERE id_gebruiker = ?");
$stmt->bindValue(1, $_SESSION['id']);
$stmt->execute();
$gebruiker = $stmt->fetchObject();

$stmt = $con->prepare(
    "SELECT activa.id_activa, activa.waarde, datum_aankoop, activa.materieel, activa.id_gebruiker, activa_soort.soort
FROM activa
JOIN activa_soort ON activa.id_activa_soort = activa_soort.id_activa_soort
WHERE id_gebruiker = ?"
);
$stmt->bindvalue(1, $_SESSION['id']);
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
        <a href="uitgaven.php">Uitgaven</a>
        <a href="schulden.php">Schulden</a>
        <a class="active" href="activa.php">Activa</a>
        <a href="logout.php">Uitloggen</a>

    </div>
</div>

<br>


<h1>Activa <a href="addactiva.php" class="btn btn-primary">+</a></h1>
<table class='table table-striped'>
    <thead class='table-dark>'>
    <th>Waarde</th>
    <th>Datum</th>
    <th>Soort</th>
    <th>Materieel</th>
    <th></th>
    </thead>
    <tbody>
    <?php
    foreach ($tests as $test) {
        $newDate = date("d-m-Y", strtotime($test->datum_aankoop));
        echo "<tr>";
        echo "<td>€" . str_replace('.', ',', $test->waarde) . "</td>";
        echo "<td>$newDate</td>";
        echo "<td>$test->soort</td>";

        if ($test->materieel == 1) {
            echo "<td>Ja</td>";
        } else {
            echo "<td>Nee</td>";
        }

        echo "<td><a class='btn btn-danger' href='activa.php?id=$test->id_activa' onclick='return confirm(\"Weet je het zeker?\");'>X</a></td>";

        echo "</tr>";
    }
    ?>
    </tbody>
</table>

</body>

</html>