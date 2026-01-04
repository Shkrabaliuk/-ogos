<?php
namespace App\Controllers\Api;

use App\Config\Database;
use App\Services\Csrf;
use App\Services\CommentHandler;

class CommentController
{
    private $pdo;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->pdo = Database::connect();
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        // Verify CSRF token
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        $handler = new CommentHandler($this->pdo);

        try {
            $handler->addComment(
                $_POST['post_id'],
                $_POST['author_name'],
                $_POST['content'],
                $_POST['parent_id'] ?? null
            );

            header('Location: ' . ($_POST['redirect_url'] ?? '/'));
            exit;
        } catch (\Exception $e) {
            // Store error in session for display
            $_SESSION['comment_error'] = $e->getMessage();
            $_SESSION['comment_data'] = $_POST;
            header('Location: ' . ($_POST['redirect_url'] ?? '/') . '#comments');
            exit;
        }
    }
}
