<?php 
// Парсимо контент через Neasden
require_once __DIR__ . '/../includes/ContentParser.php';
$parser = new ContentParser();

if (empty($post)): ?>
    <p style="color: #999;">Пост не знайдено</p>
<?php else: ?>
    
    <article class="post">
        <div class="post-meta">
            <?= date('d.m.Y', strtotime($post['created_at'])) ?>
            
            <?php if (!empty($post['author_name'])): ?>
                • <?= htmlspecialchars($post['author_name']) ?>
            <?php endif; ?>
        </div>
        
        <h1 class="post-title">
            <?= htmlspecialchars($post['title']) ?>
        </h1>
        
        <div class="post-content">
            <?= $parser->parse($post['content']) ?>
        </div>
        
        <?php if ($post['type'] === 'image' && !empty($post['gallery_images'])): ?>
            <!-- Fotorama gallery for image posts -->
            <div class="fotorama" data-nav="thumbs" data-width="100%" data-ratio="16/9">
                <?php foreach ($post['gallery_images'] as $img): ?>
                    <img src="<?= htmlspecialchars($img) ?>" alt="">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($tags)): ?>
            <div class="post-tags" style="margin-top: 24px; font-size: 14px;">
                <?php foreach ($tags as $tag): ?>
                    <a href="/tag/<?= urlencode($tag['name']) ?>" style="margin-right: 12px; color: #556677;">
                        #<?= htmlspecialchars($tag['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
    
    <?php if (!empty($comments)): ?>
        <section class="comments">
            <h2 class="comments-heading">
                Коментарі (<?= count($comments) ?>)
            </h2>
            
            <?php foreach ($comments as $comment): ?>
                <div class="comment <?= $comment['parent_id'] ? 'reply' : '' ?>">
                    <div class="comment-userpic">
                        <?php if (!empty($comment['userpic'])): ?>
                            <img src="<?= htmlspecialchars($comment['userpic']) ?>" alt="">
                        <?php endif; ?>
                    </div>
                    
                    <div class="comment-content">
                        <div class="comment-date">
                            <span class="comment-author">
                                <?= htmlspecialchars($comment['author_name']) ?>
                            </span>
                            <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                        </div>
                        
                        <div class="comment-text">
                            <?= nl2br(htmlspecialchars($comment['content'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
    
<?php endif; ?>
