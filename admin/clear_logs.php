<?php
// admin/clear_logs.php - Очистка логів помилок

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Перевірка авторизації
requireAuth();

$errorLog = ini_get('error_log');
if (empty($errorLog) || $errorLog === 'syslog') {
    // Шукаємо стандартні локації
    $possiblePaths = [
        __DIR__ . '/../error_log',
        __DIR__ . '/../php_errors.log',
        $_SERVER['DOCUMENT_ROOT'] . '/error_log',
        '/var/log/php_errors.log'
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $errorLog = $path;
            break;
        }
    }
}

if ($errorLog && file_exists($errorLog) && is_writable($errorLog)) {
    // Очищаємо файл логів
    file_put_contents($errorLog, '');
    
    // Логуємо дію
    error_log("Logs cleared by admin at " . date('Y-m-d H:i:s'));
}

// Повертаємося на settings
header('Location: /admin/settings.php');
exit;
