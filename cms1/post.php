<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$post = get_post($id);
if (!$post) {
    header("Location: 404.php");
    exit;
}

$pageTitle = $post['title'];
require 'includes/templates/header.php';
?>

<main class="site-main">
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <article>
            <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 24px; line-height: 1.2;">
                <?= htmlspecialchars($post['title']) ?>
            </h1>

            <div class="post-meta" style="padding-bottom: 24px; margin-bottom: 32px; border-bottom: 1px solid var(--border);">
                <span>
                    <i class="fas fa-clock"></i>
                    <?= time_ago($post['created_at']) ?>
                </span>
                <span>
                    <i class="fas fa-book-open"></i>
                    <?= estimate_reading_time($post['content']) ?> хв читання
                </span>
                <?php if (is_admin()): ?>
                    <a href="/admin/post-editor.php?id=<?= $post['id'] ?>" style="color: var(--accent); text-decoration: none;">
                        <i class="fas fa-edit"></i>
                        Редагувати
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($post['tags'])): ?>
                <div class="post-tags" style="margin-bottom: 32px;">
                    <?php foreach (parse_tags($post['tags']) as $tag): ?>
                        <a href="/?search=<?= urlencode($tag) ?>" class="tag">
                            <i class="fas fa-tag"></i>
                            <?= htmlspecialchars($tag) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="content" style="font-size: 18px; line-height: 1.8; color: var(--text);">
                <?= markdown($post['content']) ?>
            </div>
        </article>

        <div style="margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--border);">
            <a href="/index.php" class="btn">
                <i class="fas fa-arrow-left"></i>
                Назад до всіх постів
            </a>
        </div>
    </div>
</main>

<?php require 'includes/templates/footer.php'; ?>
