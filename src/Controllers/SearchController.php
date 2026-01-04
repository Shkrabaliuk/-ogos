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
                error_log("Search query: " . $q);

                // Use LIKE search for better Cyrillic support
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
                    ORDER BY 
                        CASE 
                            WHEN title LIKE ? THEN 1
                            ELSE 2
                        END,
                        created_at DESC
                    LIMIT 20
                ");
                $searchTerm = '%' . $q . '%';
                $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
                $results = $stmt->fetchAll();

                error_log("Search results count: " . count($results));

                // Add basic relevance score and format snippets
                foreach ($results as &$result) {
                    $titleMatch = stripos($result['title'], $q) !== false ? 2 : 0;
                    $contentMatch = stripos($result['snippet'], $q) !== false ? 1 : 0;
                    $result['relevance'] = $titleMatch + $contentMatch;
                    $result['snippet'] = htmlspecialchars($result['snippet']) . '...';
                }
                unset($result);

            } catch (\Exception $e) {
                error_log("Search error: " . $e->getMessage());
                $error = "Помилка пошуку";
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $isAdmin = isset($_SESSION['admin_id']);
        ob_start();
        include __DIR__ . '/../../templates/search_results.php';
        $childView = ob_get_clean();
        require __DIR__ . '/../../templates/layout.php';
    }
}
