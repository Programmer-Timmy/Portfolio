<?php
spl_autoload_register(function ($className) {
    $baseNamespace = 'classes';

    $classFile = str_replace($baseNamespace, '', $className);
    $classFile = str_replace('\\', '/', $classFile);

    if ($_SERVER["REQUEST_URI"] === '/admin/') {
        $filePath = 'classes/' . $classFile . '.php';
    } else {
        $filePath = 'classes/' . $classFile . '.php';
    }

        require_once $filePath;
}); 
