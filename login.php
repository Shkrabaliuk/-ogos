<?php
/**
 * Сторінка входу для адміністратора
 * Мінімалістичний дизайн без зайвих елементів
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

// Якщо вже авторизований - редірект на головну
if (isLoggedIn()) {
    header('Location: /');
    exit;
}

$error = null;

// Обробка форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Заповніть всі поля';
    } else {
        $result = attemptLogin($pdo, $username, $password);
        
        if ($result['success']) {
            // Успішний вхід - редірект на головну або на попередню сторінку
            $redirect = $_GET['redirect'] ?? '/';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = $result['error'];
        }
    }
}

$pageTitle = 'Вхід';
$childView = __DIR__ . '/views/login.php';
require __DIR__ . '/views/layout.php';
