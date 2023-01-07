<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Home</title>


</head>

<body>
    <header>
        <div class="container">
            <h1 class="logo">Tim van der Kloet</h1>
            <a href="javascript:void(0);" class="icon" onclick="MyFunction()">
                <i class="fa fa-bars"></i>
            </a>
            <nav id="nav">
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
        <div class="project-home">
            <a href="gastenboek/index.php"><img src="img/Gastenboek.png" class="img-size" alt=""></a>
            <h1>Gastenboek</h1>
        </div>
        <div class="project-home">
            <a href="duckhunt/index.html"><img src="img/duckhunt.png" class="img-size" alt=""></a>
            <h1>Duckhunt</h1>
        </div>
        <div class="project-home">
            <a href="radiogaga/index.html"><img src="img/radiogaga.png" class="img-size" alt=""></a>
            <h1>RadioGaGa</h1>
        </div>
    </div>

    <script src="js/nav.js"></script>
</body>

</html>