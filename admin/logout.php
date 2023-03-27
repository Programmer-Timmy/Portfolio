<?php 
session_start();
if (isset($_SESSION)); {
    session_unset();
    session_destroy();
    echo('<script>if (confirm("Wil je opnieuw inloggen?") == true) {
        window.location.replace("../admin");
        } else {
        window.location.replace("/");
        }</script>');
}