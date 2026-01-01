<?php 
// Парсимо контент через Neasden
require_once __DIR__ . '/../includes/ContentParser.php';
require_once __DIR__ . '/../includes/csrf.php';
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
            <div class="post-tags">
                <?php foreach ($tags as $tag): ?>
                    <a href="/tag/<?= urlencode($tag['name']) ?>" class="tag">
                        #<?= htmlspecialchars($tag['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
    
    <!-- Форма додавання коментаря -->
    <section class="comment-form-section">
        <h3 class="comment-form-heading">Додати коментар</h3>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/post_comment.php" class="comment-form">
            <?= csrfField() ?>
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <input type="hidden" name="redirect_url" value="/<?= htmlspecialchars($post['slug']) ?>">
            
            <div class="form-group">
                <label for="author_name">Ім'я</label>
                <input 
                    type="text" 
                    id="author_name" 
                    name="author_name" 
                    required 
                    maxlength="100"
                    class="form-input"
                    placeholder="Ваше ім'я"
                >
            </div>
            
            <div class="form-group">
                <label for="content">Коментар</label>
                <textarea 
                    id="content" 
                    name="content" 
                    required 
                    maxlength="5000"
                    rows="5"
                    class="form-textarea"
                    placeholder="Ваш коментар..."
                ></textarea>
            </div>
            
            <button type="submit" class="btn-submit">Відправити</button>
        </form>
    </section>
    
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