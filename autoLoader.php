<?php
spl_autoload_register(function ($className) {
    $baseNamespace = 'classes'; // Replace with your base namespace

    $classFile = str_replace($baseNamespace, '', $className);
    $classFile = str_replace('\\', '/', $classFile);

    $filePath = __DIR__ . '/classes/' . $classFile . '.php'; // Replace with the path to your classes

    if (file_exists($filePath)) {
        require_once $filePath;
    }
});
