<?php
spl_autoload_register(function ($class) {
    $directories = [
        'core/',
        'models/',
        'controllers/',
        'helpers/'
    ];
    
    foreach ($directories as $directory) {
        $file = BASE_PATH . '/' . $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});