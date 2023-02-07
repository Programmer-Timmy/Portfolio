<?php
error_reporting(E_ALL ^ E_DEPRECATED);

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
