<?php
namespace App\Services;

use App\Config\Database;
use PDO;

class Auth
{
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) || isset($_SESSION['admin_id']); // Сумісність з обома варіантами
    }

    public static function require()
    {
        if (!self::check()) {
            http_response_code(401);
            $loginUrl = '/'; // Або сторінка логіну, якщо є окрема
            header("Location: $loginUrl");
            exit;
        }
    }

    public static function login($password)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $pdo = Database::connect();
        try {
            // Check in users table for admin role
            $stmt = $pdo->prepare("SELECT id, email, password_hash FROM users WHERE role = 'admin' LIMIT 1");
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['admin_email'] = $user['email'];
                return ['success' => true];
            }
        } catch (\PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error'];
        }

        return ['success' => false, 'error' => 'Invalid credentials'];
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
    }
}
