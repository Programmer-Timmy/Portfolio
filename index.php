<?php
include 'includes/requierd.php';
$filename = substr($_SERVER['REDIRECT_URL'], 1);
if($filename == ''){
	$filename = 'home';
}
if (file_exists($filename.'.php')) {
  	include($filename.'.php');
}elseif (file_exists($filename.'.html')) {
  	include($filename.'.html');
}else{
  	include('404.php');
}
require_once "includes/footer.html";