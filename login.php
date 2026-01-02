<?php
/**
 * API endpoint для входу адміністратора (тільки пароль)
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

// Тільки POST запити
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $password = $_POST['password'] ?? '';
    
    if (empty($password)) {
        echo json_encode([
            'success' => false,
            'error' => 'Введіть пароль'
        ]);
        exit;
    }
    
    // Перевіряємо пароль без логіну
    $result = attemptLoginWithPassword($pdo, $password);
    echo json_encode($result);
    exit;
}

// Якщо не POST - редірект на головну
header('Location: /');
exit;
