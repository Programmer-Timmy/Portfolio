<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ERROR | E_PARSE);
set_exception_handler(function($exception){
    echo "<h1> oops er is iets fout gegaan</h1>";
});