<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="portfoliostyle.css">
    <title>Projects</title>

</head>

<body>
    <header>
        <div class="container">

            <h1 class="logo">Tim van der Kloet</h1>

            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="Contact.php">Contact</a></li>
                    <li><a href="">Projects</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="welcome">
        <h1>Projects</h1>
    </div>

    <div class="borderp">
        <div class="project-home" id="pro1">
            <h1>Gastenboek</h1>
        </div>
        <div class="project-home" id="pro2">
            <h1>Duckhunt</h1>
        </div>
        <div class="project-home" id="pro3">
            <h1>RadioGaGa</h1>
        </div>
        <div class="project-home" id="proj4">
            <h1>Basic jQuery</h1>
        </div>
        <div class="project-home" id="proj5">
            <h1>Game controler</h1>
        </div>
        <div class="project-home" id="proj6">
            <h1>Database (game)</h1>
        </div>
</body>
<script>
    // * naar andere pagina
    document.getElementById("pro1").onclick = function() {
        location.href = "gastenboek/index.php";
    };

    document.getElementById("pro2").onclick = function() {
        location.href = "duckhunt/index.html";
    };

    document.getElementById("pro3").onclick = function() {
        location.href = "RadioGaGa/index.html";
    };

    document.getElementById("proj4").onclick = function() {
        location.href = "BasicJqury/index.html";
    };

    document.getElementById("proj5").onclick = function() {
        location.href = "gamecontroler/index.html";
    };
    document.getElementById("proj6").onclick = function() {
        location.href = "database/index.php";
    };
</script>

</html>