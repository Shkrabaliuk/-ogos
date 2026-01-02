<?php
/**
 * PSR-4 автозавантажувач для бібліотек Rose та Neasden
 */

spl_autoload_register(function ($class) {
    // Rose бібліотека (S2\Rose\...)
    if (strpos($class, 'S2\\Rose\\') === 0) {
        $file = __DIR__ . '/../assets/libs/rose/' . str_replace('\\', '/', substr($class, 8)) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
    
    // Neasden бібліотека (Neasden\...)
    if (strpos($class, 'Neasden\\') === 0) {
        $file = __DIR__ . '/../assets/libs/neasden/' . str_replace('\\', '/', substr($class, 8)) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
