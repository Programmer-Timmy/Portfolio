<?php
date_default_timezone_set("Europe/Amsterdam");
session_start();
require 'database.php';
if (!isset($_SESSION['id'])) {
    header("location: index.php");
}

//JSON CodE
if ($_POST) {
    if ($_POST['plus_min'] == 1) {
        global $con;

        $stmt = $con->prepare("INSERT INTO inkomen(bedrag, datum, periodiek, id_inkomen_soort, id_gebruiker) VALUES (?, ?, ?, ?, ?)");
    } else {
        global $con;

        $stmt = $con->prepare("INSERT INTO uitgaven(bedrag, datum, vaste_uitgaven, id_uitgaven_soort, id_gebruiker) VALUES (?, ?, ?, ?, ?)");
    }

    $stmt->bindValue(1, $_POST['bedrag']);
    $stmt->bindValue(2, $_POST['date']);
    if (isset($_POST['periodiek'])) {
        $stmt->bindValue(3, 1);
    } else {
        $stmt->bindValue(3, 0);
    }
    $stmt->bindValue(4, $_POST['soort']);
    $stmt->bindValue(5, $_SESSION['id']);
    $stmt->execute();

    header("location: portal.php");
}


?>


<!DOCTYPE html>
<html>

<head>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        #atakan {
            width: 500px;
        }
    </style>
</head>

<body>
<div class='container-fluid'>
    <div class="card mx-auto" id="atakan">
        <div class="card-header">
            Transactie toevoegen
        </div>
        <div class="card-body">
            <h5 class="card-title"></h5>

            <form method="post">
                <div class="form-group">
                    <label for="exampleInputEmail1">Prijs in Euro's</label>
                    <input type="number" class="form-control" id="waarde" name="bedrag" aria-describedby="emailHelp"
                           step=".01">
                </div>

                <div class="form-group">
                    <label for="exampleInputEmail1">datum</label>
                    <input type="date" class="form-control" id="date" name="date" aria-describedby="emailHelp"
                           step=".01">
                </div>

                <strong>Inkomsten en uitgaven</strong>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="plus_min" id="plus" value="1" checked>
                    <label class="form-check-label" for="Inkomst">
                        Inkomst
                    </label>
                    <input type="radio" id="min" name="plus_min" value="0">
                    <label for="uitgave">Uitgave</label>
                </div>
                <div class="form-check mb-4">
                </div>
                <input type="checkbox" id="periodiek" name="periodiek" value="1">
                <label for="periodiek">Deze betaling is perodiek</label><br>


                <strong>Soort transactie</strong>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="soort" id="exampleRadios1" value="1" checked>
                    <label class="form-check-label" for="exampleRadios1">
                        Vaste lasten
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="soort" id="exampleRadios1" value="2" checked>
                    <label class="form-check-label" for="exampleRadios1">
                        Recreatie
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="soort" id="exampleRadios1" value="3" checked>
                    <label class="form-check-label" for="exampleRadios1">
                        Huishoudelijke
                    </label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="radio" name="soort" id="exampleRadios1" value="4" checked>
                    <label class="form-check-label" for="exampleRadios1">
                        Anders
                    </label>

                </div>
                <input type="submit" value="Submit">

            </form>

        </div>
    </div>
</div>
</body>

</html>