<?php

declare(strict_types=1);

// Define base path
define('BASE_PATH', dirname(__DIR__));

// PSR-4 Autoloader
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load environment variables
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Error handling
$appEnv = $_ENV['APP_ENV'] ?? 'production';
$appDebug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);

if ($appEnv === 'production' && !$appDebug) {
    ini_set('display_errors', '0');
    error_reporting(0);
    
    set_exception_handler(function (Throwable $e): void {
        http_response_code(500);
        echo '500 Internal Server Error';
        
        // Log error
        $logFile = BASE_PATH . '/storage/logs/error.log';
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $message = sprintf(
            "[%s] %s: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        
        error_log($message, 3, $logFile);
    });
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// Bootstrap application
try {
    $container = new App\Core\Container();
    $router = new App\Core\Router();
    $security = new App\Core\Security();

    // Register services in container
    $container->singleton(App\Core\Router::class, fn() => $router);
    $container->singleton(App\Core\Security::class, fn() => $security);

    // Define routes
    $router->get('/', function (): void {
        http_response_code(200);
        echo '<h1>CMS4Blog</h1><p>Welcome to CMS4Blog - Fast, Lightweight & Secure PHP CMS</p>';
    });

    // Dispatch router
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $router->dispatch($method, $uri);
    
} catch (Throwable $e) {
    if ($appDebug) {
        throw $e;
    }
    
    http_response_code(500);
    echo '500 Internal Server Error';
}
