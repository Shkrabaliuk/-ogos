<?php 
// Парсимо контент через Neasden
require_once __DIR__ . '/../includes/ContentParser.php';
$parser = new ContentParser();

if (empty($posts)): ?>
    <div class="post">
        <p style="color: #999;">Поки що тут тихо...</p>
    </div>
<?php else: ?>
    
    <?php foreach ($posts as $post): ?>
        <article class="post">
            <div class="post-meta">
                <?= date('d.m.Y', strtotime($post['created_at'])) ?>
            </div>
            
            <h2 class="post-title">
                <a href="/<?= htmlspecialchars($post['slug']) ?>">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
            </h2>
            
            <div class="post-content">
                <?= $parser->parse($post['content']) ?>
            </div>
            
            <?php
            // Завантажуємо теги для поста
            $stmt = $pdo->prepare("
                SELECT t.* 
                FROM tags t
                JOIN post_tags pt ON t.id = pt.tag_id
                WHERE pt.post_id = ?
                ORDER BY t.name
            ");
            $stmt->execute([$post['id']]);
            $tags = $stmt->fetchAll();
            ?>
            
            <?php if (!empty($tags)): ?>
                <div class="post-tags">
                    <?php foreach ($tags as $tag): ?>
                        <a href="/tag/<?= urlencode($tag['name']) ?>" class="tag">
                            #<?= htmlspecialchars($tag['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        </article>
    <?php endforeach; ?>

<?php endif; ?>