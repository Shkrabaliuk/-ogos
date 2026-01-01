<?php
header('Content-Type: application/xml; charset=utf-8');
require_once 'config/db.php';

$stmt = $pdo->query("
    SELECT * FROM posts 
    WHERE is_published = 1 
    ORDER BY created_at DESC 
    LIMIT 20
");
$posts = $stmt->fetchAll();

// Отримуємо налаштування
$settings = [];
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
foreach ($stmt->fetchAll() as $row) {
    $settings[$row['key']] = $row['value'];
}

$blogTitle = $settings['blog_title'] ?? '/\ogos';
$blogDescription = $settings['blog_description'] ?? 'Just another minimal blog';
$baseUrl = 'http://' . $_SERVER['HTTP_HOST'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title><?= htmlspecialchars($blogTitle) ?></title>
    <link><?= $baseUrl ?>/</link>
    <description><?= htmlspecialchars($blogDescription) ?></description>
    <language>uk</language>
    <atom:link href="<?= $baseUrl ?>/rss.php" rel="self" type="application/rss+xml" />
    
    <?php foreach ($posts as $post): ?>
    <item>
      <title><?= htmlspecialchars($post['title']) ?></title>
      <link><?= $baseUrl ?>/<?= htmlspecialchars($post['slug']) ?></link>
      <guid><?= $baseUrl ?>/<?= htmlspecialchars($post['slug']) ?></guid>
      <pubDate><?= date('r', strtotime($post['created_at'])) ?></pubDate>
      <description><?= htmlspecialchars(strip_tags(mb_substr($post['content'], 0, 300))) ?>...</description>
    </item>
    <?php endforeach; ?>
  </channel>
</rss>