<?php
namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;

    public static function connect()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Try to load .env if available
        if (file_exists(__DIR__ . '/../../.env') && class_exists('Dotenv\Dotenv')) {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
            $dotenv->safeLoad();
        }

        // Get config from .env or fallback to db.php
        if (isset($_ENV['DB_HOST'])) {
            // Use .env configuration
            $host = $_ENV['DB_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASSWORD'];
        } else {
            // Fallback to db.php (legacy support)
            $configPath = __DIR__ . '/db.php';
            if (!file_exists($configPath)) {
                throw new \Exception('Database configuration not found. Please run installer or create .env file.');
            }
            $config = require $configPath;
            $host = $config['host'];
            $dbname = $config['dbname'];
            $user = $config['user'];
            $pass = $config['pass'];
        }

        // Fix localhost socket issue on macOS
        if ($host === 'localhost') {
            $host = '127.0.0.1';
        }

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$instance = new PDO($dsn, $user, $pass, $options);
            return self::$instance;
        } catch (PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }
}
