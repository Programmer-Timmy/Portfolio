<?php
session_start();
require 'database.php';
if (!isset($_SESSION['id'])) {
    header("location: index.php");
}

if (isset($_GET["id"])) {
    $stmt = $con->prepare("DELETE FROM schuld WHERE id_schuld = ?");
    $stmt->bindValue(1, $_GET["id"]);

    $stmt->execute();
    header("location: schulden.php");
}

$stmt = $con->prepare("SELECT * FROM gebruiker WHERE id_gebruiker = ?");
$stmt->bindValue(1, $_SESSION['id']);
$stmt->execute();
$gebruiker = $stmt->fetchObject();

$stmt = $con->prepare(
    "SELECT schuld.id_schuld, schuld.waarde, datum_schuld, schuld.waarborg, schuld.id_gebruiker, schuld_soort.soort
FROM schuld
JOIN schuld_soort ON schuld.id_schuld_soort = schuld_soort.id_schuld_soort
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
                                            echo ("Hallo $gebruiker->voornaam $gebruiker->achternaam");

                                            ?></a>
        <div class="header-right">
            <a href="portal.php">Home</a>
            <a href="inkomsten.php">Inkomsten</a>
            <a href="uitgaven.php">Uitgaven</a>
            <a class="active" href="schulden.php">Schulden</a>
            <a href="activa.php">Activa</a>
            <a href="logout.php">Uitloggen</a>


        </div>
    </div>

    <border style="padding: 100px">
        <h1>Schuld <a href="addschuld.php" class="btn btn-primary">+</a></h1>
        <table class='table table-striped'>
            <thead class='table-dark>'>
                <th>Bedrag</th>
                <th>Datum</th>
                <th>Soort</th>
                <th>Waarborg</th>
                <th></th>
            </thead>
            <tbody>
                <?php
                foreach ($tests as $test) {
                    $newDate = date("d-m-Y", strtotime($test->datum_schuld));  
                echo "<tr>";
                echo "<td>€" . str_replace('.', ',', $test->waarde) . "</td>";
                echo "<td>$newDate</td>";
                echo "<td>$test->soort</td>";

                    if ($test->waarborg == 1) {
                        echo "<td>Ja</td>";
                    } else {
                        echo "<td>Nee</td>";
                    }

                    echo "<td><a class='btn btn-danger' href='schulden.php?id=$test->id_schuld' onclick='return confirm(\"Weet je het zeker?\");'>X</a></td>";

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </border>

</body>

</html>