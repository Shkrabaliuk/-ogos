<?php
/**
 * Збереження змін посту (inline редагування)
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

// Перевіряємо авторизацію
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

// Перевірка CSRF
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    die('Невірний CSRF токен');
}

$post_id = $_POST['post_id'] ?? null;
$title = trim($_POST['title'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$content = $_POST['content'] ?? '';
$redirect_url = $_POST['redirect_url'] ?? '/';

// Валідація
if (empty($title) || empty($slug) || empty($content)) {
    die('Всі поля обов\'язкові');
}

// Перевірка формату slug
if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
    die('Slug може містити тільки латинські літери, цифри та дефіси');
}

try {
    if ($post_id) {
        // Оновлення існуючого посту
        $stmt = $pdo->prepare("
            UPDATE posts 
            SET title = ?, slug = ?, content = ?
            WHERE id = ?
        ");
        $stmt->execute([$title, $slug, $content, $post_id]);
        
        // Редирект на оновлений URL (якщо slug змінився)
        header("Location: /$slug");
    } else {
        // Створення нового посту
        $stmt = $pdo->prepare("
            INSERT INTO posts (title, slug, content, type, is_published) 
            VALUES (?, ?, ?, 'text', 1)
        ");
        $stmt->execute([$title, $slug, $content]);
        
        header("Location: /$slug");
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        die('Помилка: пост з таким slug вже існує');
    }
    error_log("Save post error: " . $e->getMessage());
    die('Помилка збереження посту');
}
exit;
