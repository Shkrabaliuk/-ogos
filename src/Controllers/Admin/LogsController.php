<?php
namespace App\Controllers\Admin;

use App\Services\Auth;

class LogsController
{
    public function __construct()
    {
        Auth::require();
    }

    public function clear()
    {
        $errorLog = ini_get('error_log');

        if (empty($errorLog) || $errorLog === 'syslog') {
            // Search standard locations
            $possiblePaths = [
                __DIR__ . '/../../../error_log',
                __DIR__ . '/../../../php_errors.log',
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
            // Clear log file
            file_put_contents($errorLog, '');

            // Log the action
            error_log("Logs cleared by admin at " . date('Y-m-d H:i:s'));
        }

        // Redirect back to settings
        header('Location: /admin/settings');
        exit;
    }
}
