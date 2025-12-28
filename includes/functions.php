<?php
// includes/functions.php

// 1. Запускаємо сесію (щоб пам'ятати адміна)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Перевірка: чи авторизований користувач
 */
function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Отримати всі пости
 */
function get_posts() {
    global $pdo;
    // Перевірка, чи існує змінна $pdo
    if (!isset($pdo)) {
        return [];
    }
    $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

/**
 * Отримати один пост за ID
 */
function get_post($id) {
    global $pdo;
    if (!isset($pdo)) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Підрахунок часу читання (в хвилинах)
 */
function estimate_reading_time($text) {
    $words = str_word_count(strip_tags($text));
    $minutes = ceil($words / 200);
    return $minutes > 0 ? $minutes : 1;
}

/**
 * --- ОСЬ ЦЯ ФУНКЦІЯ, ЯКОЇ НЕ ВИСТАЧАЛО ---
 * Обрізає текст до певної довжини (для прев'ю на головній)
 */
function excerpt($text, $limit = 300) {
    // Спочатку чистимо від HTML тегів, щоб не порізати посеред тегу
    $text = strip_tags($text);
    
    // Якщо текст коротший за ліміт — повертаємо як є
    if (mb_strlen($text) <= $limit) return $text;
    
    // Обрізаємо
    $text = mb_substr($text, 0, $limit);
    
    // Шукаємо останній пробіл, щоб не різати слово посередині
    $lastSpace = mb_strrpos($text, ' ');
    if ($lastSpace !== false) {
        $text = mb_substr($text, 0, $lastSpace);
    }
    
    return $text . '...';
}