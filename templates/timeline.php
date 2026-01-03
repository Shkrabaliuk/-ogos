<?php
use App\Services\Render;
use App\Services\Csrf;
// $posts passed from Controller


if (empty($posts)): ?>
    <section>
        <p>Поки що тут тихо...</p>
    </section>
<?php else: ?>

    <!-- Навігація вгорі (новіші пости) -->
    <?php if (isset($page) && $page > 1): ?>
        <nav>
            <a href="/?page=<?= $page - 1 ?>">
                ↑ Читати вище
            </a>
        </nav>
    <?php endif; ?>

    <!-- Форма створення нового посту -->
    <?php if ($isAdmin): ?>
        <details>
            <summary role="button">Новий пост</summary>

            <form method="POST" action="/admin/save_post">
                <?= Csrf::field() ?>
                <input type="hidden" name="redirect_url" value="/">

                <label>Заголовок
                    <input type="text" name="title" id="new_title" required>
                </label>

                <label>URL (slug)
                    <input type="text" name="slug" id="new_slug" required pattern="[a-z0-9\-]+">
                    <small>Тільки латиниця, цифри та дефіси</small>
                </label>

                <label>Контент (Neasden)
                    <textarea id="newPostContent" name="content" required rows="10"></textarea>
                </label>

                <p>
                    <small><strong>Синтаксис:</strong> # Заголовок • **жирний** • //курсив// • - список</small>
                </p>

                <!-- Simple image upload hint (drag & drop logic script is in footer) -->
                <p id="newPostDropzone">
                    Перетягніть картинки сюди
                </p>

                <button type="submit">Створити</button>
            </form>
        </details>

        <script>
            // Автогенерація slug з заголовка
            document.getElementById('new_title')?.addEventListener('input', function (e) {
                const slugInput = document.getElementById('new_slug');
                if (!slugInput.dataset.manual) {
                    slugInput.value = e.target.value
                        .toLowerCase()
                        .replace(/[^a-z0-9\s\-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim();
                }
            });

            document.getElementById('new_slug')?.addEventListener('input', function () {
                this.dataset.manual = 'true';
            });
        </script>
        <hr>
    <?php endif; ?>

    <?php foreach ($posts as $post): ?>
        <article>
            <header>
                <h2>
                    <a href="/<?= htmlspecialchars($post['slug']) ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                </h2>
            </header>

            <div>
                <?= Render::html($post['content']) ?>
            </div>

            <footer>
                <p>
                    <span title="<?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>">
                        <?= date('d.m.Y', strtotime($post['created_at'])) ?>
                    </span>

                    <?php if ($isAdmin): ?>
                        | <a href="/<?= htmlspecialchars($post['slug']) ?>#edit">Редагувати</a>
                    <?php endif; ?>

                    <?php
                    // Завантажуємо теги
                    $tags = [];
                    try {
                        $stmt = $pdo->prepare("
                            SELECT t.*
                            FROM tags t
                            JOIN post_tags pt ON t.id = pt.tag_id
                            WHERE pt.post_id = ?
                            ORDER BY t.name
                        ");
                        $stmt->execute([$post['id']]);
                        $tags = $stmt->fetchAll();
                    } catch (PDOException $e) {
                    }
                    ?>

                    <?php if (!empty($tags)): ?>
                        <br>Теги:
                        <?php foreach ($tags as $tag): ?>
                            <a href="/tag/<?= urlencode($tag['name']) ?>">
                                #<?= htmlspecialchars($tag['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </p>
            </footer>
        </article>
        <hr>
    <?php endforeach; ?>

    <!-- Навігація внизу (старіші пости) -->
    <?php if (isset($page) && isset($totalPages) && $page < $totalPages): ?>
        <nav>
            <a href="/?page=<?= $page + 1 ?>">
                Читати нижче ↓
            </a>
        </nav>
    <?php endif; ?>

<?php endif; ?>