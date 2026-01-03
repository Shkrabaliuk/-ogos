<?php
namespace App\Controllers;

use App\Config\Database;

class SitemapController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function index()
    {
        // Set XML headers
        header('Content-Type: application/xml; charset=utf-8');

        // Get all published posts
        $stmt = $this->pdo->query("
            SELECT slug, created_at 
            FROM posts 
            WHERE is_published = 1 
            ORDER BY created_at DESC
        ");
        $posts = $stmt->fetchAll();

        // Determine base URL
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'];

        // Generate Sitemap XML
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        ?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            <url>
                <loc>
                    <?= htmlspecialchars($baseUrl) ?>/
                </loc>
                <changefreq>daily</changefreq>
                <priority>1.0</priority>
            </url>

            <?php foreach ($posts as $post): ?>
                <url>
                    <loc>
                        <?= htmlspecialchars($baseUrl . '/' . $post['slug']) ?>
                    </loc>
                    <lastmod>
                        <?= date('c', strtotime($post['created_at'])) ?>
                    </lastmod>
                    <changefreq>monthly</changefreq>
                    <priority>0.8</priority>
                </url>
            <?php endforeach; ?>
        </urlset>
        <?php
    }
}
