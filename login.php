<?php
/**
 * API endpoint для входу адміністратора
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

// Тільки POST запити
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'error' => 'Заповніть всі поля'
        ]);
        exit;
    }
    
    $result = attemptLogin($pdo, $username, $password);
    echo json_encode($result);
    exit;
}

// Якщо не POST - редірект на головну
header('Location: /');
exit;
