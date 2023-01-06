<?php
require_once "database.php";
$stmt = $con->prepare("SELECT * FROM games WHERE id=?");
$stmt->bindValue(1, $_GET["id"]);
$stmt->execute();

$games = $stmt->fetchObject();
if ($_POST) {
    $stmt = $con->prepare("UPDATE games SET naam=?, genre=?, leeftijd=?, prijs=?, online=?, platform=? where id=?");
    $stmt->bindValue(1, htmlspecialchars($_POST["name"]));
    $stmt->bindValue(2, htmlspecialchars($_POST["genre"]));
    $stmt->bindValue(3, htmlspecialchars($_POST["leeftijd"]));
    $stmt->bindValue(4, htmlspecialchars($_POST["prijs"]));
    $stmt->bindValue(5, htmlspecialchars($_POST["online"]));
    $stmt->bindValue(6, htmlspecialchars($_POST["platform"]));
    $stmt->bindValue(7, $_GET["id"]);

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
    <h1>Game wijzigen</h1>
    <form method="post" class="form-group">
        Naam: <input type="text" name="name" required maxlength="13" value="<?php echo $games->naam; ?>"></br>
        Genre: <select name=" genre">
            <option value="Sports" <?php if ($games->genre == "Sports") {echo "selected"; } ?>>Sports</option>
            <option value="Simulation" <?php if ($games->genre == "Simulation") {echo "selected"; } ?>>Simulation</option>
            <option value="Race" <?php if ($games->genre == "Race") {echo "selected"; } ?>>Race</option>
            <option value="Platform" <?php if ($games->genre == "Platform") {echo "selected"; } ?>>Platform</option>
            <option value="RPG" <?php if ($games->genre == "RPG") {echo "selected"; } ?>>RPG</option>
            <option value="Shooter" <?php if ($games->genre == "Shooter") {echo "selected"; } ?>>Shooter</option>
            <option value="Arcade" <?php if ($games->genre == "Arcade") {echo "selected"; } ?>>Arcade</option>
            <option value="Third person" <?php if ($games->genre == "Third person") {echo "selected"; } ?>>Third person</option>
        </select></br>
        Leeftijd: <select name="leeftijd">
            <option value="Alle leeftijden" <?php if ($games->leeftijd == "Alle leeftijden") {echo "selected"; } ?>>Alle leeftijden</option>
            <option value="6+" <?php if ($games->leeftijd == "6+") {echo "selected"; } ?>>6+</option>
            <option value="16+" <?php if ($games->leeftijd == "16+") {echo "selected"; } ?>>16+</option>
            <option value="18+" <?php if ($games->leeftijd == "18+") {echo "selected"; } ?>>18+</option>
        </select></br>
        prijs: <input type="text" name="prijs" required maxlength="6" value="<?php echo $games->prijs; ?>"></br>
        <div>
            <input type='hidden' value='Nee' name='online'>
            <input type="checkbox" name="online" value="Ja" <?php if($games->online == "Ja") {echo "checked";}?>> Online multiplayer <br>
        </div>
        platform: <select name="platform">
            <option value="Playsation 4" <?php if($games->platform == "Playsation 4") {echo "selected";}?>>Playsation 4</option>
            <option value="Playsation 5" <?php if($games->platform == "Playsation 5") {echo "selected";}?>>Playsation 5</option>
            <option value="Nintendo Wii" <?php if($games->platform == "Nintendo Wii") {echo "selected";}?>>Nintendo Wii</option>
            <option value="Nintendo Switch"> <?php if($games->platform == "Nintendo Switch") {echo "selected";}?>Nintendo Switch</option>
            <option value="Xbox One" <?php if($games->platform == "Xbox One") {echo "selected";}?>>Xbox One</option>
            <option value="Pc" <?php if($games->platform == "Pc") {echo "selected";}?>>Pc</option>
        </select><Br>
        <input type="submit" value="Opslaan">
    </form>


</body>

</html>