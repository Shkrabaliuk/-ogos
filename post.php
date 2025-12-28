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

<main class="post-page">
    <div class="container">
        <article>
            <h1><?= htmlspecialchars($post['title']) ?></h1>

            <div class="post-meta" style="margin-bottom: 24px;">
                <span>🕐 <?= time_ago($post['created_at']) ?></span>
                <span>📖 <?= estimate_reading_time($post['content']) ?> хв читання</span>
                
                <?php if (is_admin()): ?>
                    <a href="/admin/post-editor.php?id=<?= $post['id'] ?>" style="color: #E67E48;">✎ Редагувати</a>
                <?php endif; ?>
            </div>

            <?php if (!empty($post['tags'])): ?>
                <div class="post-tags" style="margin-bottom: 32px;">
                    <?php foreach (parse_tags($post['tags']) as $tag): ?>
                        <a href="/?search=<?= urlencode($tag) ?>" class="tag">#<?= htmlspecialchars($tag) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="content">
                <?= markdown($post['content']) ?>
            </div>
        </article>

        <a href="/index.php" class="back-link">← Назад до всіх постів</a>
    </div>
</main>

<?php require 'includes/templates/footer.php'; ?>
