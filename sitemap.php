<?php
header('Content-Type: application/xml; charset=utf-8');
require_once 'config/db.php';

$stmt = $pdo->query("
    SELECT slug, created_at 
    FROM posts 
    WHERE is_published = 1 
    ORDER BY created_at DESC
");
$posts = $stmt->fetchAll();

$baseUrl = 'http://' . $_SERVER['HTTP_HOST'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc><?= $baseUrl ?>/</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  
  <?php foreach ($posts as $post): ?>
  <url>
    <loc><?= $baseUrl ?>/<?= htmlspecialchars($post['slug']) ?></loc>
    <lastmod><?= date('c', strtotime($post['created_at'])) ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <?php endforeach; ?>
</urlset>