<?php
if ($_POST) {


    date_default_timezone_set("Europe/Amsterdam");
    $message = new stdClass();
    $message->name = $_POST["name"];
    $message->mass = $_POST["revieuw"];
    $message->date = date("d-m-y h:i:s");
    $message->rating = $_POST["rating"];
    $message->topic = $_POST["topic"];

    $json = json_encode($message);

    file_put_contents("data.txt", $json . "\r\n", FILE_APPEND);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="portfoliostyle.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Contact</title>
</head>

<body>
    <header>
        <div class="container">

            <h1 class="logo">Tim van der Kloet</h1>

            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="">Contact</a></li>
                    <li><a href="projects.php">Projects</a></li>
                    <!-- <li><a href="">Login</a></li> -->
                </ul>
            </nav>
        </div>
    </header>

    <div class="welcome">
        <h1>Contact</h1>
    </div>

    <div class="border-re">
        <div id="contekst">
            <h2>You can contact me whit: </h2>
            <h3>School email: <a href="mailto:567589@edu.rocmn.nl">567589@edu.rocmn.nl</a><br>
                Private email: <a href="mailto:Tim.vanderkloet@gmail.com">Tim.vanderkloet@gmail.com</a><br>
                Telephone: <a href="tel:064361248">06 4361248</a></h3>
            <h2>Want to leave a review about a project?</h2>
            <!-- todo make a revieuw place! -->
            <button id="revieuwbutton">Leave a review</button>
            <script>
                $(document).ready(function() {
                    $("#revieuwbutton").click(function() {
                        $("#revieuwpopup").animate({
                            top: '10vh'
                        })
                    })
                })
            </script>
        </div>
        <div id="revieuw">
            <h2>Reviews:</h2>
            <?php
            foreach (file("data.txt") as $line) {

                $message = json_decode($line);

                $naam = $message->name;
                $bericht = $message->mass;
                $datum = $message->date;
                $ster = $message->rating;
                $onderwerp = $message->topic;



                echo ("<div class='text'><h2>datum: " . $datum . "<br>Rating: " . $ster . "<br>Name: " . $naam . "<br>Topic: " . $onderwerp . "<h2><h3>Message: " . $bericht . "</h3></div>");
            }
            ?>
        </div>
    </div>
    <div class="popupbox" id="revieuwpopup">
        <form method="post">
            <h1>Leave a revieuw</h1>
            <form method="post">
                <h3>Name:<br>
                    <input type="text" name="name" required> <br>
                    Topic:<Br>
                    <input type="text" name="topic" required> <BR>
                    Revieuw:<BR>
                    <textarea style="resize: none;" name="revieuw" id="" cols="60" rows="9" required></textarea><br>
                    Rating of 1 to 5:
                    <select id="rating" name="rating">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select><br>
                    <input type="submit" value="Send" id="verstuur">
                </h3>
            </form>
        </form>
    </div>
</body>

</html>