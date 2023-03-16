<?php
error_reporting(E_ALL ^ E_DEPRECATED);
set_exception_handler(function($exception){
    echo "<h1> oops er is iets fout gegaan</h1>";
});
session_start();



include 'classes/projects.php';
include 'classes/database.php';
include 'classes/databaseUtilities.php';
include 'classes/accounts.php';

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'portfolio';

$database = new Database;
$database->connect($dbhost, $dbuser, $dbpass, $dbname);
