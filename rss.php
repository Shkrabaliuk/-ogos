<?php
header('Content-Type: application/xml; charset=utf-8');
require_once 'config/db.php';
require_once 'includes/ContentParser.php';

$parser = new ContentParser();

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

$blogTitle = $settings['blog_title'] ?? '/\\ogos';
$blogDescription = $settings['blog_description'] ?? 'Мінімалістичний блог про код, дизайн та технології';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" 
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel>
    <title><?= htmlspecialchars($blogTitle) ?></title>
    <link><?= $baseUrl ?>/</link>
    <description><?= htmlspecialchars($blogDescription) ?></description>
    <language>uk</language>
    <lastBuildDate><?= date('r') ?></lastBuildDate>
    <atom:link href="<?= $baseUrl ?>/rss.php" rel="self" type="application/rss+xml" />
    <generator>/\ogos CMS</generator>
    
    <?php foreach ($posts as $post): ?>
    <?php
        // Отримуємо теги для поста
        $stmt = $pdo->prepare("
            SELECT t.name 
            FROM tags t
            JOIN post_tags pt ON t.id = pt.tag_id
            WHERE pt.post_id = ?
            ORDER BY t.name
        ");
        $stmt->execute([$post['id']]);
        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Парсимо контент
        $fullContent = $parser->parse($post['content']);
        $excerpt = $parser->getExcerpt($post['content'], 300);
    ?>
    <item>
      <title><?= htmlspecialchars($post['title']) ?></title>
      <link><?= $baseUrl ?>/<?= htmlspecialchars($post['slug']) ?></link>
      <guid isPermaLink="true"><?= $baseUrl ?>/<?= htmlspecialchars($post['slug']) ?></guid>
      <pubDate><?= date('r', strtotime($post['created_at'])) ?></pubDate>
      <description><?= htmlspecialchars($excerpt) ?></description>
      <content:encoded><![CDATA[<?= $fullContent ?>]]></content:encoded>
      <?php foreach ($tags as $tag): ?>
      <category><?= htmlspecialchars($tag) ?></category>
      <?php endforeach; ?>
    </item>
    <?php endforeach; ?>
  </channel>
</rss>