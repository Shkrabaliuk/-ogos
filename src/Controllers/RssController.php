<?php
namespace App\Controllers;

use App\Config\Database;
use App\Services\Render;

class RssController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function index()
    {
        // Set XML headers
        header('Content-Type: application/rss+xml; charset=utf-8');

        // Get blog settings
        $stmt = $this->pdo->query("SELECT `key`, `value` FROM settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = $row['value'];
        }

        $blogTitle = $settings['site_title'] ?? 'Logos Blog';
        $blogDescription = $settings['site_description'] ?? '';
        $blogUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

        // Get latest posts
        $stmt = $this->pdo->query("
            SELECT * FROM posts 
            WHERE is_published = 1 
            ORDER BY created_at DESC 
            LIMIT 20
        ");
        $posts = $stmt->fetchAll();

        // Generate RSS XML
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        ?>
        <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
            <channel>
                <title>
                    <?= htmlspecialchars($blogTitle) ?>
                </title>
                <link>
                <?= htmlspecialchars($blogUrl) ?>
                </link>
                <description>
                    <?= htmlspecialchars($blogDescription) ?>
                </description>
                <language>uk</language>
                <atom:link href="<?= htmlspecialchars($blogUrl) ?>/rss.php" rel="self" type="application/rss+xml" />

                <?php foreach ($posts as $post): ?>
                    <item>
                        <title>
                            <?= htmlspecialchars($post['title']) ?>
                        </title>
                        <link>
                        <?= htmlspecialchars($blogUrl . '/' . $post['slug']) ?>
                        </link>
                        <guid>
                            <?= htmlspecialchars($blogUrl . '/' . $post['slug']) ?>
                        </guid>
                        <pubDate>
                            <?= date('r', strtotime($post['created_at'])) ?>
                        </pubDate>
                        <description>
                            <![CDATA[<?= Render::html($post['content_raw'] ?? $post['content']) ?>]]>
                        </description>
                    </item>
                <?php endforeach; ?>
            </channel>
        </rss>
        <?php
    }
}
