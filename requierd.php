<?php
session_start();
if (!isset($_SESSION['access'])) {
    $_SESSION['access'] = "none";
}

include 'classes/database.php';
include 'classes/databaseUtilities.php';


$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'portfolio';

$database = new Database;
$database->connect($dbhost, $dbuser, $dbpass, $dbname);
