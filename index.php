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

<main class="site-main">
    <?php if ($search): ?>
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">
                        <i class="fas fa-search"></i>
                        Результати пошуку: "<?= htmlspecialchars($search) ?>"
                    </h2>
                    <p style="color: var(--text-light); font-size: 14px; margin-top: 4px;">
                        Знайдено постів: <?= $total ?>
                    </p>
                </div>
                <a href="/index.php" class="btn">
                    <i class="fas fa-times"></i>
                    Очистити
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (count($posts) > 0): ?>
        <div class="posts-grid">
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <h2>
                        <a href="/post.php?id=<?= $post['id'] ?>">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h2>

                    <div class="post-excerpt">
                        <?= nl2br(htmlspecialchars(excerpt($post['content'], 180))) ?>
                    </div>

                    <?php if (!empty($post['tags'])): ?>
                        <div class="post-tags">
                            <?php foreach (parse_tags($post['tags']) as $tag): ?>
                                <a href="?search=<?= urlencode($tag) ?>" class="tag">
                                    <i class="fas fa-tag"></i>
                                    <?= htmlspecialchars($tag) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-meta">
                        <span>
                            <i class="fas fa-clock"></i>
                            <?= time_ago($post['created_at']) ?>
                        </span>
                        <span>
                            <i class="fas fa-book-open"></i>
                            <?= estimate_reading_time($post['content']) ?> хв
                        </span>
                        <?php if (is_admin()): ?>
                            <a href="/admin/post-editor.php?id=<?= $post['id'] ?>" style="color: var(--accent); text-decoration: none;">
                                <i class="fas fa-edit"></i>
                                Редагувати
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                        <i class="fas fa-chevron-left"></i>
                        Попередня
                    </a>
                <?php endif; ?>
                
                <span class="btn" style="pointer-events: none;">
                    Сторінка <?= $page ?> з <?= $total_pages ?>
                </span>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="active">
                        Наступна
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="card">
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <?php if ($search): ?>
                    <h3>Нічого не знайдено</h3>
                    <p>За запитом "<strong><?= htmlspecialchars($search) ?></strong>" не знайдено жодного поста</p>
                    <a href="/index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i>
                        Показати всі пости
                    </a>
                <?php else: ?>
                    <h3>Поки що немає постів</h3>
                    <p>Створіть свій перший пост, щоб почати</p>
                    <?php if (is_admin()): ?>
                        <a href="/admin/post-editor.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Написати пост
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require 'includes/templates/footer.php'; ?>
