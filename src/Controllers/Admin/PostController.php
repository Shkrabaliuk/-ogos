<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Services\Auth;
use App\Services\Csrf;

class PostController
{
    private $pdo;

    public function __construct()
    {
        Auth::require();
        $this->pdo = Database::connect();
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        // Verify CSRF
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            die('Невірний CSRF токен');
        }

        $post_id = $_POST['post_id'] ?? null;
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $content = $_POST['content'] ?? '';

        // Validation
        if (empty($title) || empty($slug) || empty($content)) {
            die('Всі поля обов\'язкові');
        }

        // Check slug format
        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
            die('Slug може містити тільки латинські літери, цифри та дефіси');
        }

        try {
            if ($post_id) {
                // Update existing post
                $stmt = $this->pdo->prepare("
                    UPDATE posts 
                    SET title = ?, slug = ?, content_raw = ?, is_published = 1
                    WHERE id = ?
                ");
                $stmt->execute([$title, $slug, $content, $post_id]);

                // Redirect to updated URL
                header("Location: /$slug");
            } else {
                // Create new post
                $stmt = $this->pdo->prepare("
                    INSERT INTO posts (title, slug, content_raw, is_published) 
                    VALUES (?, ?, ?, 1)
                ");
                $stmt->execute([$title, $slug, $content]);

                header("Location: /$slug");
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                die('Помилка: пост з таким slug вже існує');
            }
            error_log("Save post error: " . $e->getMessage());
            die('Помилка збереження посту');
        }
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            die('Невірний CSRF токен');
        }

        $post_id = $_POST['post_id'] ?? null;

        if ($post_id) {
            try {
                $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->execute([$post_id]);
            } catch (\PDOException $e) {
                error_log("Delete post error: " . $e->getMessage());
                die('Помилка видалення посту');
            }
        }

        header('Location: /');
        exit;
    }
}
