<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));

$posts = get_posts($search, 'DESC', $page);
$total = get_total_posts($search);
$per_page = (int)get_setting('posts_per_page', 10);
$total_pages = ceil($total / $per_page);

$pageTitle = $search ? "Пошук: $search" : "";
require 'includes/templates/header.php';
?>

<main>
    <div class="container">
        <?php if ($page > 1): ?>
            <div class="pagination-top">
                <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">← Пізніше</a>
                <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="active">Сп +</a>
            </div>
        <?php endif; ?>

        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <article>
                    <h2>
                        <a href="post.php?id=<?= $post['id'] ?>">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h2>

                    <div class="content">
                        <?= nl2br(htmlspecialchars(excerpt($post['content'], 250))) ?>
                    </div>

                    <div class="post-meta">
                        <span><?= estimate_reading_time($post['content']) ?> хв</span>
                        <?php if (is_admin()): ?>
                            <a href="/admin/post-editor.php?id=<?= $post['id'] ?>" style="color: #E67E48;">✎ Ред</a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($post['tags'])): ?>
                        <div class="post-tags">
                            <?php foreach (parse_tags($post['tags']) as $tag): ?>
                                <a href="?search=<?= urlencode($tag) ?>" class="tag">#<?= htmlspecialchars($tag) ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>

            <?php if ($total_pages > 1 && $page < $total_pages): ?>
                <div class="pagination-bottom">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">← Раніше</a>
                    <?php endif; ?>
                    <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="active">Сп +</a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-state">
                <?php if ($search): ?>
                    <p>😕 Нічого не знайдено за запитом "<strong><?= htmlspecialchars($search) ?></strong>"</p>
                    <a href="index.php">← Показати всі пости</a>
                <?php else: ?>
                    <p>📝 Тут поки порожньо</p>
                    <?php if (is_admin()): ?>
                        <br><a href="/admin/post-editor.php" class="btn btn-primary">Написати пост</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require 'includes/templates/footer.php'; ?>
