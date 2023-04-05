<?php
session_start();
require_once "../database.php";

if (!isset($_SESSION['admin'])) {
    header('location: ../');
}

if ($_POST) {

    if (isset($_POST["isadmin"])) {
        $isadmin = 1;
    } else {
        $isadmin = 0;
    }

    $pass = password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT);

    $stmt = $con->prepare("INSERT INTO gebruiker(voornaam, achternaam, isadmin, gebruikersnaam, wachtwoord, id_land) VALUES(?,?,?,?,?,?)");
    $stmt->bindValue(1, $_POST["voornaam"]);
    $stmt->bindValue(2, $_POST["achternaam"]);
    $stmt->bindValue(3, $isadmin);
    $stmt->bindValue(4, $_POST["gebruikersnaam"]);
    $stmt->bindValue(5, $pass);
    $stmt->bindValue(6, $_POST["id_land"]);
    $stmt->execute();

    header("location: ../admin");
}

$stmt = $con->prepare("SELECT * FROM land");
$stmt->execute();

$landen = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script>
        function myFunction() {
            var x = document.getElementById("LaatZien");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
</head>

<body style="padding: 0; margin: 0;">
    <form method="post" style="padding-top: 100px; padding-left: 500px; padding-right: 500px;">
        <div class="mb-3">
            voornaam: <input type="text" name="voornaam" class="form-control"><br>
        </div>
        <div class="mb-3">
            achernaam: <input type="text" name="achternaam" class="form-control"><br>
        </div>
        <div class="mb-3">
            Admin? <input type="checkbox" name="isadmin" type="checkbox" class="form-check-input"><br>
        </div>
        <div class="mb-3">
            Gebruikersnaam: <input type="text" name="gebruikersnaam" class="form-control"><br>
        </div>
        wachtwoord: <input type="password" name="wachtwoord" class="form-control" id="LaatZien"><br>
        <input type="checkbox" onclick="myFunction()">Laat wachtwoord zien
        <div class="mb-3">
            land:
            <select id="id_land" name="id_land" class="form-select">
                <?php
                foreach ($landen as $land) {
                    echo "<option  value='$land->id_land'>$land->naam</option>";
                }
                ?>
            </select>
        </div>
        <input class="btn btn-primary" type="submit">
    </form>
</body>

</html>