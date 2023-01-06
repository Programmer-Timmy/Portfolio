<?php
    if(isset($_COOKIE["Auction_Item"])){
        echo '<script>alert("u kunt niet meer dan 1 bericht plaatsen"); window.location.href = "index.php";</script>';
    
    }
    if($_POST) {
        if(isset($_COOKIE["Auction_Item"])){
            echo '<script>alert("U kunt niet nog een bericht plaatsen"); window.location.href = "index.php";</script>';
            
        }
        else {
            date_default_timezone_set("Europe/Amsterdam");
        $message = new stdClass();
        $message->name = $_POST["name"];
        $message->massage = $_POST["massage"];
        $message->date = date("d-m-y h:i:s");

        $json = json_encode($message);

        file_put_contents("data.txt", $json. "\r\n", FILE_APPEND);
        
        
        setcookie("Auction_Item", "Luxury Car", time() + 2 * 24 * 60 * 60);
    
        header("Location: index.php");
        }
    
    }
    
?>


<!DOCTYPE html>
<html>
    <head>
        <title>Toevoegen</title>
        <style>
            html{
                margin-top: 0px;
                padding: 0px;
            }
            body{
                padding: 0px;
                margin-top: 0px;
                background-image: url(img/the-sky-15.jpg);
                background-repeat: no-repeat;
                background-size: 250vh;
                background-attachment: fixed;
            }
            form{
                margin-top: 5vh;
                border-radius: 3vh;
                background-color: skyblue;
                font-size: 3vh;
                height: 80vh;
                width: 80vh;
                margin-left: auto;
                margin-right: auto;
                align-items: center;
                border: black 0.2vh solid;
                text-align: center;
            }
            textarea{
                width: 60vh;
                height: 50vh;
                border: black 0.2vh solid;
                font-size: 2vh;
                text-align: center;
            }
            #name{
                border: black 0.2vh solid;
                height: 3vh;
                text-align: center;
                font-size: 2vh;
            }
            #submit{
                border: 0.2vh black solid;
                height: 4vh;
                width: 30vh;
                font-size: 3vh;
                border-radius: 50vh;
            }
        </style>
    </head>
    <body>
        <form method="POST">
            Typ uw naam:<Br> 
            <input id="name" name="name" />
            <br>Typ uw bericht:<br>
            <textarea name="massage"></textarea>
            <br>
            <input id="submit" type="submit" value="verstuur">
        </form>
    </body>
</html>