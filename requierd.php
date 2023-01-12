<?php
session_start();
if (!isset($_SESSION['access'])) {
    $_SESSION['access'] = "none";
}
include 'db.php';

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'portfolio';

$db = new db($dbhost, $dbuser, $dbpass, $dbname);
