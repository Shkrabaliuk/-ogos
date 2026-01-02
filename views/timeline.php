<?php 
// Парсимо контент через Neasden
require_once __DIR__ . '/../includes/ContentParser.php';
$parser = new ContentParser();

if (empty($posts)): ?>
    <div class="post">
        <p class="empty-message">Поки що тут тихо...</p>
    </div>
<?php else: ?>
    
    <!-- Навігація вгорі (новіші пости) -->
    <?php if (isset($page) && $page > 1): ?>
        <div class="pagination pagination-top">
            <a href="/?page=<?= $page - 1 ?>" class="pagination-link pagination-prev">
                <i class="fas fa-arrow-up"></i>
                Читати вище
            </a>
        </div>
    <?php endif; ?>
    
    <!-- Кнопка "Новий пост" для адмінів -->
    <?php if ($isAdmin): ?>
        <div class="admin-actions mb-40 text-center">
            <a href="/admin/editor.php" class="btn-new-post">
                <i class="fas fa-plus"></i>
                Новий пост
            </a>
        </div>
    <?php endif; ?>
    
    <?php foreach ($posts as $post): ?>
        <article class="post">
            <div class="post-meta">
                <?= date('d.m.Y', strtotime($post['created_at'])) ?>
                
                <?php if ($isAdmin): ?>
                    <span class="admin-controls">
                        <a href="/admin/editor.php?id=<?= $post['id'] ?>" class="edit-link" title="Редагувати">
                            <i class="fas fa-pen"></i>
                        </a>
                    </span>
                <?php endif; ?>
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
    
    <!-- Навігація внизу (старіші пости) -->
    <?php if (isset($page) && isset($totalPages) && $page < $totalPages): ?>
        <div class="pagination pagination-bottom">
            <a href="/?page=<?= $page + 1 ?>" class="pagination-link pagination-next">
                Читати нижче
                <i class="fas fa-arrow-down"></i>
            </a>
        </div>
    <?php endif; ?>

<?php endif; ?>