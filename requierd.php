<?php
//error_reporting(E_ALL ^ E_DEPRECATED);
//error_reporting(E_ERROR | E_PARSE);
//set_exception_handler(function($exception){
//   echo "<h1> oops er is iets fout gegaan</h1>";
//});
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