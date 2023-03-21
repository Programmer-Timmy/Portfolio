<?php

    $con = new PDO("mysql:host=localhost;dbname=portfolio", "root", "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
?>