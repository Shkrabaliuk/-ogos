<?php
session_start();
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['is_admin'] = true;
        header("Location: ../index.php");
    } else {
        header("Location: ../index.php?error=1");
    }
    exit;
}

header("Location: ../index.php");
