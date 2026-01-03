<?php
namespace App\Controllers;

use App\Config\Database;
use App\Services\Render;

class PostController
{
    public function show($slug)
    {
        $pdo = Database::connect();

        // 1. Отримуємо пост
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ? AND is_published = 1 LIMIT 1");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();

        // 404 Not Found
        if (!$post) {
            http_response_code(404);
            require __DIR__ . '/../../templates/header.php';
            echo "<div style='text-align:center; padding: 5rem 1rem;'><h1>404</h1><p>Цей запис не знайдено.</p></div>";
            require __DIR__ . '/../../templates/footer.php';
            return;
        }

        // 2. Лічильник переглядів (+1)
        $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);

        // 3. Підготовка контенту (Markdown -> HTML)
        // Якщо в базі вже є HTML - беремо його, якщо ні - рендеримо на льоту
        if (!empty($post['content_html'])) {
            $post['content'] = $post['content_html'];
        } else {
            $post['content'] = Render::html($post['content_raw'] ?? '');
        }

        // 4. Коментарі
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at ASC");
        $stmt->execute([$post['id']]);
        $comments = $stmt->fetchAll();

        // 5. Теги (Заглушка, щоб уникнути помилки в шаблоні)
        $tags = [];

        // 6. Глобальні налаштування для Header
        $blogSettings = [];
        $settingsStmt = $pdo->query("SELECT `key`, `value` FROM settings");
        while ($row = $settingsStmt->fetch()) {
            $blogSettings[$row['key']] = $row['value'];
        }

        // Змінні для View
        $blogTitle = $blogSettings['site_title'] ?? 'Logos Blog';
        $pageTitle = $post['title'];

        // 7. Рендеринг
        require __DIR__ . '/../../templates/header.php';
        require __DIR__ . '/../../templates/post.php';
        require __DIR__ . '/../../templates/footer.php';
    }
}
