<?php
if ($_POST) {
    require_once "database.php";

    $stmt = $con->prepare("INSERT INTO games(naam, genre, leeftijd, prijs, online, platform) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bindValue(1, htmlspecialchars($_POST["name"]));
    $stmt->bindValue(2, htmlspecialchars($_POST["genre"]));
    $stmt->bindValue(3, htmlspecialchars($_POST["leeftijd"]));
    $stmt->bindValue(4, htmlspecialchars($_POST["prijs"]));
    $stmt->bindValue(5, htmlspecialchars($_POST["online"]));
    $stmt->bindValue(6, htmlspecialchars($_POST["platform"]));

    $stmt->execute();

    header("location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">

    <title>Document</title>
</head>

<body>
    <h1>Nieuwe game toevoegen</h1>
    <form method="post" class="form-group">
        Naam: <input type="text" name="name" required maxlength="13"></br>
        Genre: <select name="genre">
            <option value="Sports">Sports</option>
            <option value="Simulation">Simulation</option>
            <option value="Race">Race</option>
            <option value="Platform">Platform</option>
            <option value="RPG">RPG</option>
            <option value="Shooter">Shooter</option>
            <option value="Arcade">Arcade</option>
            <option value="Third person">Third person</option>
        </select></br>
        Leeftijd: <select name="leeftijd">
            <option value="Alle leeftijden">Alle leeftijden</option>
            <option value="6+">6+</option>
            <option value="16+">16+</option>
            <option value="18+">18+</option>
        </select></br>
        prijs: <input type="text" name="prijs" required maxlength="6"></br>
        <div>
            <input type='hidden' value='Nee' name='online'>
            <input type="checkbox" name="online" value="Ja"> Online multiplayer <br>
        </div>
        platform: <select name="platform">
            <option value="Playsation 4">Playsation 4</option>
            <option value="Playsation 5">Playsation 5</option>
            <option value="Nintendo Wii">Nintendo Wii</option>
            <option value="Nintendo Switch">Nintendo Switch</option>
            <option value="Xbox One">Xbox One</option>
            <option value="Pc">Pc</option>
        </select><Br>
        <input type="submit" value="Opslaan">
    </form>


</body>

</html>