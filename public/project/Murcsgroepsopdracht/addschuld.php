<?php
date_default_timezone_set("Europe/Amsterdam");
session_start();
require 'database.php';

if (!isset($_SESSION['id'])) {
    header("location: index.php");
}
//JSON CodE
if ($_POST) {
    global $con;
    $stmt = $con->prepare("INSERT INTO schuld(waarde, datum_schuld, waarborg, id_schuld_soort, id_gebruiker) VALUES (?, ?, ?, ?, ?)");

    $stmt->bindValue(1, $_POST['waarde']);
    $stmt->bindValue(2, $_POST['date']);
    if (isset($_POST['waarborg'])) {
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
            Schuld toevoegen
        </div>
        <div class="card-body">
            <h5 class="card-title"></h5>

            <form method="post">
                <div class="form-group">
                    <label for="exampleInputEmail1">Waarde in Euro's</label>
                    <input type="number" class="form-control" id="waarde" name="waarde" aria-describedby="emailHelp"
                           step=".01">
                </div>

                <div class="form-group">
                    <label for="exampleInputEmail1">datum</label>
                    <input type="date" class="form-control" id="date" name="date" aria-describedby="emailHelp"
                           step=".01">
                </div>

                <input type="checkbox" id="waarborg" name="waarborg" value="1">
                <label for="waarborg">Er is waarborg op deze schuld:</label><br>


                <strong>Soort schuld</strong>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="soort" id="exampleRadios1" value="1" checked>
                    <label class="form-check-label" for="exampleRadios1">
                        Hypotheek
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="soort" id="exampleRadios1" value="2" checked>
                    <label class="form-check-label" for="exampleRadios1">
                        Korte lening
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="soort" id="exampleRadios1" value="3" checked>
                    <label class="form-check-label" for="exampleRadios1">
                        Creditcard
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