<?php
error_reporting(E_ERROR | E_PARSE);

require_once __DIR__ . '/config/settings.php';
require_once __DIR__ . '/controllers/Database.php';
require_once __DIR__ . '/controllers/Videos.php';

Videos::add();