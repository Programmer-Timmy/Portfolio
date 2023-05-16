<?php
require_once 'settings.php';
session_start();

if (isset($_SESSION['discard_after']) && time() > $_SESSION['discard_after']) {
    session_unset();
    session_destroy();
    session_start();
}

require'autoLoader.php';

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'portfolio';

$database = new Database;
$database->connect($dbhost, $dbuser, $dbpass, $dbname);