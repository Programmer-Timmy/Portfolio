<?php

foreach (file("data.txt") as $line) {

    $message = json_decode($line);

    $naam = $message->name;
    $bericht = $message->massage;
    $date = $message->date;

    echo("<div class='text'><h2>datum: " . $date . "<br>Naam: " . $naam . "<h2><h3>bericht: " . $bericht . "</h3></div>");
}
if ($_POST) {
    if (isset($_COOKIE["Auction_Item"])) {
        setcookie("Auction_Item", "Luxury Car", time() - 3600);
        echo '<script>alert("u bent afgemeld");</script>';
    } else {
        echo '<script>alert("u bent afgemeld");</script>';
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <style>
        html {
            margin-top: 0;
            padding: 0;
        }

        body {
            padding: 0;
            margin-top: 0;
            background-image: url(img/the-sky-15.jpg);
            background-repeat: no-repeat;
            background-size: 250vh;
            background-attachment: fixed;
        }

        .text {
            background-color: skyblue;
            word-wrap: break-word;
            border: 0.2vh solid black;
            width: 100vh;
            margin: 2vh;
            height: auto;
            margin-left: auto;
            margin-right: auto;
            border-radius: 2vh;
        }

        h2 {
            margin-right: 1vh;
            margin-left: 1vh;
            font-size: 3.7vh;
        }

        h3 {
            margin-left: 1vh;
            margin-right: 1vh;
            font-size: 2.5vh;
        }

        #voegtoe {
            font-size: 2.5vh;
            border-radius: 2vh;
            position: fixed;
            top: 2vh;
            right: 2vh;
            background-color: white;
            height: 4vh;
            width: 15vh;

        }

        #meldaf {
            font-size: 2.5vh;
            border-radius: 2vh;
            position: fixed;
            top: 2vh;
            left: 2vh;
            background-color: white;
            height: 4vh;
            width: 15vh;

        }
    </style>
</head>
<body>
<a href="toevoegen.php">
    <button id="voegtoe">Voeg toe +</button>
</a>
<a href="afmelden.php">
    <form method="post">
        <input id="meldaf" type="submit" name="afmelden" value="afmelden">
    </form>


</body>
</html>