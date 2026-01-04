<?php
use App\Services\Render;
use App\Services\Csrf;

if (empty($post)): ?>
    <section>
        <p>Пост не знайдено</p>
    </section>
<?php else: ?>

    <!-- Post Content -->
    <article id="postView" style="position: relative;">
        <!-- Admin Edit Button (Sticky) -->
        <?php if ($isAdmin): ?>
            <div class="admin-floating-actions">
                <a href="#edit" onclick="toggleEditMode(); return false;" class="btn-edit-sticky" title="Редагувати">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </a>
            </div>
            <style>
                .admin-floating-actions {
                    position: absolute;
                    top: 0;
                    right: 0;
                    height: 100%;
                    pointer-events: none;
                    /* Allow clicks through the container */
                }

                .btn-edit-sticky {
                    position: sticky;
                    top: 100px;
                    float: right;
                    margin-right: -50px; /* Adjusted margin */
                    width: 32px; /* Smaller touch target */
                    height: 32px;
                    background: transparent; /* No background */
                    color: #2e7d32; /* Green icon */
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
                    color: #1b5e20; /* Darker green on hover */
                }

                /* Mobile adaptation: overlay bottom right */
                @media (max-width: 800px) {
                    .admin-floating-actions {
                        position: fixed;
                        top: auto;
                        bottom: 2rem;
                        right: 2rem;
                        height: auto;
                        z-index: 100;
                    }

                    .btn-edit-sticky {
                        position: static;
                        float: none;
                        margin-right: 0;
                    }
                }
            </style>
        <?php endif; ?>

        <header>
            <h1><?= htmlspecialchars($post['title']) ?></h1>
            <p style="color: #999; margin: 0.5rem 0;">
                <?= date('d.m.Y', strtotime($post['created_at'])) ?>
                <?php if (!empty($tags)): ?>
                    <?php foreach ($tags as $tag): ?>
                        · <a href="/tag/<?= urlencode($tag['name']) ?>">#<?= htmlspecialchars($tag['name']) ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </p>
        </header>

        <div style="margin: 2rem 0;">
            <?= $post['content'] ?>
        </div>
    </article>

    <!-- Edit Mode (Admin Only) -->
    <?php if ($isAdmin): ?>
        <div id="postEdit" hidden>
            <h2>Редагування посту</h2>
            <form method="POST" action="/admin/save_post">
                <?= Csrf::field() ?>
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <input type="hidden" name="redirect_url" value="/<?= htmlspecialchars($post['slug']) ?>">

                <div class="form-group">
                    <label for="edit_title">Заголовок</label>
                    <input type="text" id="edit_title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="edit_slug">URL (slug)</label>
                    <input type="text" id="edit_slug" name="slug" value="<?= htmlspecialchars($post['slug']) ?>" required
                        pattern="[a-z0-9\-]+">
                    <small>Тільки латиниця, цифри та дефіси</small>
                </div>

                <div class="form-group">
                    <label for="content">Контент (Neasden)</label>
                    <textarea id="content" name="content" required
                        rows="20"><?= htmlspecialchars($post['content']) ?></textarea>
                    <small><strong>Синтаксис:</strong> # Заголовок · **жирний** · //курсив// · - список</small>
                </div>

                <div class="form-actions">
                    <button type="submit">Зберегти</button>
                    <button type="button" onclick="toggleEditMode()">Скасувати</button>
                </div>
            </form>
        </div>

        <script>
            function toggleEditMode() {
                const viewMode = document.getElementById('postView');
                const editMode = document.getElementById('postEdit');

                if (viewMode.hidden) {
                    viewMode.hidden = false;
                    editMode.hidden = true;
                    window.location.hash = '';
                } else {
                    viewMode.hidden = true;
                    editMode.hidden = false;
                    document.getElementById('edit_title').focus();
                    window.location.hash = 'edit';
                }
            }

            // Auto-open edit mode if #edit in URL
            if (window.location.hash === '#edit') {
                toggleEditMode();
            }
        </script>
    <?php endif; ?>

    <hr style="margin: 3rem 0;">

    <!-- Comments Section -->
    <section id="comments">
        <?php if (!empty($comments)): ?>
            <h2>Коментарів: <?= count($comments) ?></h2>
        <?php else: ?>
            <h2>Коментарі поки відсутні</h2>
            <p style="color: #999;">Чи не бажаєте написати перший?</p>
        <?php endif; ?>

        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <article style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #eee;">
                    <header style="margin-bottom: 0.5rem;">
                        <strong><?= htmlspecialchars($comment['author_name']) ?></strong>
                        <small style="color: #999; margin-left: 0.5rem;">
                            <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                        </small>
                    </header>
                    <div>
                        <?= nl2br(htmlspecialchars($comment['content'])) ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Comment Form -->
        <div style="margin-top: 2rem;">
            <h3>Залишити коментар</h3>

            <?php if (isset($_SESSION['comment_error'])): ?>
                <p style="color: #d00;"><?= htmlspecialchars($_SESSION['comment_error']) ?></p>
                <?php unset($_SESSION['comment_error']); ?>
            <?php endif; ?>

            <form method="POST" action="/api/post_comment.php">
                <?= Csrf::field() ?>
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <input type="hidden" name="redirect_url" value="/<?= htmlspecialchars($post['slug']) ?>">

                <div class="form-group">
                    <label for="author_name">Ім'я</label>
                    <input type="text" id="author_name" name="author_name" required maxlength="100" placeholder="Ваше ім'я">
                </div>

                <div class="form-group">
                    <label for="comment_content">Коментар</label>
                    <textarea id="comment_content" name="content" required maxlength="5000" rows="5"
                        placeholder="Ваш коментар..."></textarea>
                </div>

                <button type="submit">Відправити</button>
            </form>
        </div>
    </section>

<?php endif; ?>