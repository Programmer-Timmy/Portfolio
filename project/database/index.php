<?php

require_once "database.php";

//verwijderen

if (isset($_GET["id"])) {
    $stmt = $con->prepare("DELETE FROM games WHERE id= ?");
    $stmt->bindValue(1, $_GET["id"]);

    $stmt->execute();

    header("location: index.php");
}

$stmt = $con->prepare("SELECT * FROM games");
$stmt->execute();
$games = $stmt->fetchAll(pdo::FETCH_OBJ);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <header class="d-flex p-3 justify-content-between">
        <h2>Mijn Games (<?php ?>)</h2>
        <a href="add.php" class="btn btn-secondary">Spel toevoegen</a>
    </header>
    <container class="d-flex p-5 justify-content-between align-content-start flex-wrap ">

        <?php
        foreach ($games as $games) {

            echo "
                <div class='game'>
                    <header class='d-flex p-2 justify-content-between'>
                    <h1> $games->naam</h1>
                    <div>
                        <a href='edit.php?id=$games->id' class='btn btn-info'>...</a>
                        <a class='btn btn-danger' href='index.php?id=$games->id' onclick='return confirm(\"weet je het zeker?\");'>X</a>
                    </div>
                    </header>
                    <h3 class='d-flex p-2'>
                        Genre: $games->genre <br><br>                  
                        Leeftijd: $games->leeftijd <br><br>
                        Prijs: $games->prijs <br><br>
                        Online multiplayer: $games->online <br><br>
                        Platform: $games->platform
                    </h3>
                </div>";
        }
        ?>
        </div>
    </container>


</body>

</html>