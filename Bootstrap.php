<?php

spl_autoload_register(function ($class) {
    if (class_exists($class)) {
        return true;
    }

    $pathPsr4 = __DIR__ . '/' . strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';
    if (file_exists($pathPsr4)) {
        include_once $pathPsr4;
    }
    return true;
});

include_once __DIR__ . '/vendor/autoload.php';