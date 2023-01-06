<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Home</title>
</head>

<body>
    <header>
        <div class="container">
            <!-- !heeft logo nodigz -->
            <h1 class="logo">Tim van der Kloet</h1>

            <nav>
                <ul>
                    <li><a href="">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="projects.php">Projects</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <!-- welcome txt -->
    <div class="welcome">
        <h1>Welcome!</h1>
    </div>
    <!-- 3 projecten -->
    <div class="borderp">
        <div class="project-home" id="home1">
            <h1>Gastenboek</h1>
        </div>
        <div class="project-home" id="home2">
            <h1>Duckhunt</h1>
        </div>
        <div class="project-home" id="home3">
            <h1>RadioGaGa</h1>
        </div>
    </div>

    <script>
        // * naar andere pagina
        document.getElementById("home1").onclick = function() {
            location.href = "gastenboek/index.php";
        };

        document.getElementById("home2").onclick = function() {
            location.href = "duckhunt/index.html";
        };

        document.getElementById("home3").onclick = function() {
            location.href = "RadioGaGa/index.html";
        };
    </script>

</body>

</html>