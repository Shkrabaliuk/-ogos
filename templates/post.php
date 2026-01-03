<?php
use App\Services\Render;
use App\Services\Csrf;
// $post and $comments are passed from Controller


if (empty($post)): ?>
    <section>
        <p>Пост не знайдено</p>
    </section>
<?php else: ?>

    <!-- Режим перегляду -->
    <article id="postView">
        <header>
            <h1><?= htmlspecialchars($post['title']) ?></h1>
        </header>

        <div>
            <?= Render::html($post['content']) ?>
        </div>

        <?php if ($post['type'] === 'image' && !empty($post['gallery_images'])): ?>
            <!-- Fotorama classes kept if critical for JS, but ideally should be replaced/removed if strict.
             If user installed fotorama locally, it expects classes. User said "Delete ALL class attributes".
             If I remove 'fotorama', the gallery breaks.
             Rule 1 Exception: "Leave class only ... if critically necessary for JavaScript".
             This is likely critical for Fotorama JS. Keeping class="fotorama". -->
            <div class="fotorama" data-nav="thumbs" data-width="100%" data-ratio="16/9">
                <?php foreach ($post['gallery_images'] as $img): ?>
                    <img src="<?= htmlspecialchars($img) ?>" alt="">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <footer>
            <nav>
                <?php if ($isAdmin): ?>
                    <button onclick="toggleEditMode()">✎ Редагувати</button>
                    |
                <?php endif; ?>

                <a href="#comments">
                    💬 <?= !empty($comments) ? count($comments) : 'Коментарі' ?>
                </a>
                |
                <span title="<?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>">
                    <?= date('d.m.Y', strtotime($post['created_at'])) ?>
                </span>

                <?php foreach ($tags as $tag): ?>
                    | <a href="/tag/<?= urlencode($tag['name']) ?>">#<?= htmlspecialchars($tag['name']) ?></a>
                <?php endforeach; ?>
            </nav>
        </footer>
    </article>

    <!-- Режим редагування -->
    <?php if ($isAdmin): ?>
        <div id="postEdit" hidden>
            <h3>Редагування посту</h3>
            <form method="POST" action="/admin/save_post">
                <?= Csrf::field() ?>
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <input type="hidden" name="redirect_url" value="/<?= htmlspecialchars($post['slug']) ?>">

                <label>Заголовок
                    <input type="text" id="edit_title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
                </label>

                <label>URL (slug)
                    <input type="text" id="edit_slug" name="slug" value="<?= htmlspecialchars($post['slug']) ?>" required
                        pattern="[a-z0-9\-]+">
                    <small>Тільки латиниця, цифри та дефіси</small>
                </label>

                <label>Контент (Neasden розмітка)
                    <textarea id="content" name="content" required
                        rows="20"><?= htmlspecialchars($post['content']) ?></textarea>
                </label>

                <!-- Drag & Drop зона -->
                <p id="imageDropzone">
                    Перетягніть картинки сюди
                </p>

                <p>
                    <small><strong>Синтаксис:</strong> # Заголовок • **жирний** • //курсив// • - список • відступ 4 пробіли для
                        коду</small>
                </p>

                <button type="submit">💾 Зберегти</button>
                <button type="button" onclick="toggleEditMode()">Скасувати</button>
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

            // Автоматично відкрити редагування, якщо в URL є #edit
            if (window.location.hash === '#edit') {
                toggleEditMode();
            }
        </script>
    <?php endif; ?>

    <hr>

    <section id="comments">
        <?php if (!empty($comments)): ?>
            <h3>Коментарі (<?= count($comments) ?>)</h3>

            <?php foreach ($comments as $comment): ?>
                <article>
                    <header>
                        <strong><?= htmlspecialchars($comment['author_name']) ?></strong>
                        <small>
                            <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                        </small>
                    </header>

                    <div>
                        <?= nl2br(htmlspecialchars($comment['content'])) ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Форма додавання коментаря -->
        <div>
            <h3>
                <?= !empty($comments) ? 'Залишити коментар' : 'Будьте першим, хто прокоментує' ?>
            </h3>

            <?php if (isset($error)): ?>
                <p style="color:red"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="/post_comment.php">
                <?= Csrf::field() ?>
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <input type="hidden" name="redirect_url" value="/<?= htmlspecialchars($post['slug']) ?>">

                <label>І'мя
                    <input type="text" id="author_name" name="author_name" required maxlength="100" placeholder="Ваше ім'я"
                        value="<?= htmlspecialchars($commentData['author_name'] ?? '') ?>">
                </label>

                <label>Коментар
                    <textarea id="content" name="content" required maxlength="5000" rows="5"
                        placeholder="Ваш коментар..."><?= htmlspecialchars($commentData['content'] ?? '') ?></textarea>
                </label>

                <button type="submit">Відправити</button>
            </form>
        </div>
    </section>

<?php endif; ?>