<?php
use App\Services\Render;
// $posts passed from Controller

if (empty($posts)): ?>
    <section>
        <p>Поки що тут тихо...</p>
    </section>
<?php else: ?>

    <?php if ($isAdmin): ?>
        <style>
            .admin-floating-actions {
                position: absolute;
                top: 0;
                right: 0;
                height: 100%;
                pointer-events: none;
            }

            .btn-edit-sticky {
                position: sticky;
                top: 100px;
                float: right;
                margin-right: -50px;
                width: 32px;
                height: 32px;
                background: transparent;
                color: #2e7d32;
                display: flex;
                align-items: center;
                justify-content: center;
                pointer-events: auto;
                transition: transform 0.2s, color 0.2s;
                border: none;
                padding: 0;
            }

            .btn-edit-sticky:hover {
                background: transparent;
                transform: scale(1.1);
                color: #1b5e20;
            }

            @media (max-width: 800px) {
                .admin-floating-actions {
                    position: absolute;
                    /* Keep relative to post on mobile timeline to avoid overlap chaos */
                    top: auto;
                    bottom: 1rem;
                    right: 0;
                    height: auto;
                    width: 100%;
                }

                .btn-edit-sticky {
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    float: none;
                    margin-right: 0;
                }
            }
        </style>
    <?php endif; ?>

    <?php foreach ($posts as $post): ?>
        <article style="margin-bottom: 4rem; position: relative;">
            <?php if ($isAdmin): ?>
                <div class="admin-floating-actions">
                    <a href="/<?= htmlspecialchars($post['slug']) ?>#edit" class="btn-edit-sticky" title="Редагувати">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </a>
                </div>
            <?php endif; ?>

            <header>
                <h2>
                    <a href="/<?= htmlspecialchars($post['slug']) ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                </h2>
            </header>

            <div style="margin: 1.5rem 0;">
                <?= Render::html($post['content']) ?>
            </div>

            <footer>
                <p style="color: #999; font-size: 0.9rem; margin: 0;">
                    <?= date('d.m.Y', strtotime($post['created_at'])) ?>

                    <?php
                    // Get comment count
                    try {
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
                        $stmt->execute([$post['id']]);
                        $commentCount = (int) $stmt->fetchColumn();

                        if ($commentCount > 0) {
                            echo ' · ' . $commentCount . ' ' . ($commentCount === 1 ? 'коментар' : 'коментарів');
                        }
                    } catch (\PDOException $e) {
                        // Comments table issue
                    }
                    ?>

                    <?php
                    // Get tags (if table exists)
                    try {
                        $stmt = $pdo->prepare("
                            SELECT t.name
                            FROM tags t
                            JOIN post_tags pt ON t.id = pt.tag_id
                            WHERE pt.post_id = ?
                            ORDER BY t.name
                        ");
                        $stmt->execute([$post['id']]);
                        $tags = $stmt->fetchAll();

                        if (!empty($tags)) {
                            foreach ($tags as $tag) {
                                echo ' · <a href="/tag/' . urlencode($tag['name']) . '">#' . htmlspecialchars($tag['name']) . '</a>';
                            }
                        }
                    } catch (\PDOException $e) {
                        // Tags table doesn't exist yet
                    }
                    ?>
                </p>
            </footer>
        </article>
    <?php endforeach; ?>

    <!-- Pagination -->
    <?php if ($page > 1 || $page < $totalPages): ?>
        <nav
            style="display: flex; justify-content: space-between; align-items: center; margin-top: 4rem; padding-top: 2rem; border-top: 1px solid #eee;">

            <?php if ($page > 1): ?>
                <a href="/?page=<?= $page - 1 ?>" style="text-decoration: none;">
                    ← Повернутись
                </a>
            <?php else: ?>
                <div></div> <!-- Spacer for flexbox -->
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="/?page=<?= $page + 1 ?>" style="text-decoration: none;">
                    Читати далі →
                </a>
            <?php else: ?>
                <div></div> <!-- Spacer for flexbox -->
            <?php endif; ?>

        </nav>
    <?php endif; ?>

<?php endif; ?>