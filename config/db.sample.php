<?php
// config/db.sample.php - Приклад конфігурації бази даних
// Скопіюйте цей файл як db.php і заповніть свої дані

// Налаштування підключення
$host = 'localhost';           // Хост бази даних
$db   = 'logos_db';           // Назва бази даних
$user = 'root';               // Користувач MySQL
$pass = 'your_password_here'; // Пароль MySQL
$charset = 'utf8mb4';

// Environment (розкоментуйте для development режиму)
// define('ENV', 'development'); // development | production

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
