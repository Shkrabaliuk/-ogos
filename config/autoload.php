<?php
/**
 * Simple PSR-4 Autoloader для Rose та Neasden
 */

spl_autoload_register(function ($class) {
    $namespaces = [
        'S2\\Rose\\' => __DIR__ . '/../assets/libs/rose/',
        'Neasden\\' => __DIR__ . '/../assets/libs/neasden/',
    ];
    
    foreach ($namespaces as $prefix => $base_dir) {
        $len = strlen($prefix);
        
        // Перевіряємо чи клас належить цьому namespace
        if (strncmp($prefix, $class, $len) === 0) {
            // Отримуємо відносний шлях класу
            $relative_class = substr($class, $len);
            
            // Замінюємо namespace separators на directory separators
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
            
            // Якщо файл існує, підключаємо
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});
