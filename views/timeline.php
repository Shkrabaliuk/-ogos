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
                <?= $parser->getExcerpt($post['content'], 400) ?>
            </div>
        </article>
    <?php endforeach; ?>

<?php endif; ?>
