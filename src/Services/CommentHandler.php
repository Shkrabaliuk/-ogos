<?php
namespace App\Services;

use PDO;
use Exception;

class CommentHandler
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function addComment($postId, $authorName, $content, $parentId = null)
    {
        // Валідація
        $authorName = trim($authorName);
        $content = trim($content);

        if (empty($authorName) || empty($content)) {
            throw new Exception('Заповніть всі поля');
        }

        if (mb_strlen($authorName) > 100) {
            throw new Exception('Ім\'я занадто довге');
        }

        if (mb_strlen($content) > 5000) {
            throw new Exception('Коментар занадто довгий');
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO comments (post_id, parent_id, author_name, content) 
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([$postId, $parentId, $authorName, $content]);
    }

    public function getComments($postId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM comments 
            WHERE post_id = ? 
            ORDER BY created_at ASC
        ");
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }
}