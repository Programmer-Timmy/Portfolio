<?php
require_once "database.php";
session_start();
if ($_POST) {
    $stmt = $con->prepare("SELECT * FROM gebruiker WHERE gebruikersnaam=?");
    $stmt->bindValue(1, $_POST["gebruikersnaam"]);
    $stmt->execute();

    $user = $stmt->fetchObject();

    if ($user !== false) {
        if (password_verify($_POST["wachtwoord"], $user->wachtwoord)) {

            $_SESSION["id"] = $user->id_gebruiker;
            if ($user->isadmin == 1) {
                $_SESSION["admin"] = 'true';
                header("location:/admin");
                return;
            }
            header("location: portal.php");
            return;
        }
    }
    echo "<script>alert('Verkeerd wachtwoord of gebruikersnaam');</script>";
}
?>

<!DOCTYPE html>
<html>

<head>

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
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body style="padding-top: 100px">
<section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-8 col-lg-6 col-xl-4">
                <form method="post">
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <p class="lead fw-normal mb-0 me-3">Login</p>
                    </div>

                    <div class="divider d-flex align-items-center my-4">
                        <p class="text-center fw-bold mx-3 mb-0"></p>
                    </div>

                    <!-- Email input -->
                    <div class="form-outline mb-4">
                        <label class="form-label" for="form3Example3">Gebruikersnaam:
                        </label>
                        <input type="text" class="form-control form-control-lg" name="gebruikersnaam"><br>


                    </div>

                    <!-- Password input -->
                    <div class="form-outline mb-3">
                        <label class="form-label" for="form3Example4">Wachtwoord:</label>
                        <input type="password" class="form-control form-control-lg" name="wachtwoord" id="LaatZien"><br>
                        <input type="checkbox" onclick="myFunction()">Laat wachtwoord zien


                    </div>


                    <div class="text-center text-lg-start mt-4 pt-2">
                        <a href="registreren.php" class="btn btn-primary btn-lg" role="button">Registreren</a>
                        <input type="submit" value="Inloggen" class="btn btn-primary btn-lg">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary"
         style="position: fixed;
    bottom: 0;
    width: 100%;">
        <!-- Copyright -->
        <div class="text-white mb-3 mb-md-0">
            Copyright © 2023. All rights reserved.
        </div>

    </div>
</section>
</body>

</html>