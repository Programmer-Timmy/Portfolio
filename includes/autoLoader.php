<?php
spl_autoload_register(function ($className) {
    $baseNamespace = 'classes';

    $classFile = str_replace($baseNamespace, '', $className);
    $classFile = str_replace('\\', '/', $classFile);

    $filePath = './classes/' . $classFile . '.php';
    if (file_exists($filePath)) {
        require_once $filePath;
    }
}); 
