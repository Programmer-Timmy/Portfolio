<?php
spl_autoload_register(function ($className) {
    $baseNamespace = '';

    $classFile = str_replace($baseNamespace, '', $className);
    $classFile = str_replace('\\', '/', $classFile);

    $filePath = __DIR__ . '/classes/' . $classFile . '.php';

    if (file_exists($filePath)) {
        require_once $filePath;
    }
}); 
