<?php
namespace App\Controllers;

use App\Config\Database;
use App\Services\View;

class HomeController
{
    public function index()
    {
        $pdo = Database::connect();

        // Settings (cache this in a real app)
        $stmt = $pdo->query("SELECT value FROM settings WHERE `key` = 'posts_per_page'");
        $postsPerPage = $stmt ? (int) $stmt->fetchColumn() : 10;
        if ($postsPerPage < 1)
            $postsPerPage = 10;

        // Pagination
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $offset = ($page - 1) * $postsPerPage;

        // Fetch Posts
        $stmt = $pdo->prepare("
            SELECT * FROM posts 
            WHERE is_published = 1 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $postsPerPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();

        // Total for pagination UI (simplified)
        $total = $pdo->query("SELECT COUNT(*) FROM posts WHERE is_published = 1")->fetchColumn();
        $totalPages = ceil($total / $postsPerPage);

        // Global Blog Settings for Header
        // In a better architecture, this would be injected into the View context globally
        $blogSettings = [];
        $settingsStmt = $pdo->query("SELECT `key`, `value` FROM settings");
        while ($row = $settingsStmt->fetch()) {
            $blogSettings[$row['key']] = $row['value'];
        }
        $blogTitle = $blogSettings['site_title'] ?? 'Logos Blog';

        // Render
        require __DIR__ . '/../../templates/header.php';
        require __DIR__ . '/../../templates/timeline.php';
        require __DIR__ . '/../../templates/footer.php';
    }
}
