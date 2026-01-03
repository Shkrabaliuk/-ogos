<?php
namespace App\Controllers;

use App\Config\Database;

class SearchController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function index()
    {
        $q = $_GET['q'] ?? '';
        $results = [];
        $error = null;

        if (!empty($q)) {
            try {
                // Use MySQL FULLTEXT search if available
                $stmt = $this->pdo->prepare("
                    SELECT 
                        id,
                        title,
                        slug,
                        SUBSTRING(content_raw, 1, 200) as snippet,
                        created_at as date,
                        MATCH(title, content_raw) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
                    FROM posts 
                    WHERE is_published = 1 
                    AND MATCH(title, content_raw) AGAINST(? IN NATURAL LANGUAGE MODE)
                    ORDER BY relevance DESC, created_at DESC
                    LIMIT 20
                ");
                $stmt->execute([$q, $q]);
                $results = $stmt->fetchAll();

                // Format snippets
                foreach ($results as &$result) {
                    $result['snippet'] = htmlspecialchars($result['snippet']) . '...';
                }
                unset($result);

            } catch (\PDOException $e) {
                // Fallback to LIKE search if FULLTEXT not available
                try {
                    $stmt = $this->pdo->prepare("
                        SELECT 
                            id,
                            title,
                            slug,
                            SUBSTRING(content_raw, 1, 200) as snippet,
                            created_at as date
                        FROM posts 
                        WHERE is_published = 1 
                        AND (title LIKE ? OR content_raw LIKE ?)
                        ORDER BY created_at DESC
                        LIMIT 20
                    ");
                    $searchTerm = '%' . $q . '%';
                    $stmt->execute([$searchTerm, $searchTerm]);
                    $results = $stmt->fetchAll();

                    // Add basic relevance score
                    foreach ($results as &$result) {
                        $titleMatch = stripos($result['title'], $q) !== false ? 2 : 0;
                        $contentMatch = stripos($result['snippet'], $q) !== false ? 1 : 0;
                        $result['relevance'] = $titleMatch + $contentMatch;
                        $result['snippet'] = htmlspecialchars($result['snippet']) . '...';
                    }
                    unset($result);
                } catch (\Exception $e2) {
                    error_log("Search error: " . $e2->getMessage());
                    $error = "Помилка пошуку";
                }
            }
        }

        // Get blog settings
        $stmt = $this->pdo->query("SELECT `key`, `value` FROM settings");
        $blogSettings = [];
        while ($row = $stmt->fetch()) {
            $blogSettings[$row['key']] = $row['value'];
        }
        $blogTitle = $blogSettings['site_title'] ?? 'Logos Blog';
        $pageTitle = $q ? "Пошук: {$q} — {$blogTitle}" : "Пошук — {$blogTitle}";

        // Render view
        $isAdmin = isset($_SESSION['admin_id']);
        ob_start();
        include __DIR__ . '/../../templates/search_results.php';
        $childView = ob_get_clean();
        require __DIR__ . '/../../templates/layout.php';
    }
}
