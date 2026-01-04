<?php
/**
 * New Post Page Template
 */

use App\Services\Csrf;

$blogTitle = $blogSettings['site_title'] ?? '/\\ogos';
$pageTitle = "Новий пост — {$blogTitle}";

ob_start();
?>

<div class="settings-container">
    <h1>Створити новий пост</h1>

    <form method="POST" action="/admin/save_post" class="settings-form">
        <?= Csrf::field() ?>
        <input type="hidden" name="redirect_url" value="/">

        <div class="form-group">
            <label for="title">Заголовок</label>
            <input type="text" name="title" id="title" required autofocus>
        </div>

        <div class="form-group">
            <label for="slug">URL (slug)</label>
            <input type="text" name="slug" id="slug" required pattern="[a-z0-9\-]+">
            <small>Тільки латиниця, цифри та дефіси. Генерується автоматично з заголовка.</small>
        </div>

        <div class="form-group">
            <label for="content">Контент (Neasden)</label>
            <textarea id="content" name="content" required rows="20"></textarea>
            <small><strong>Синтаксис:</strong> # Заголовок • **жирний** • //курсив// • - список</small>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="is_published" value="1" checked>
                Опублікувати одразу
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Створити пост</button>
            <a href="/" class="btn">Скасувати</a>
        </div>
    </form>
</div>

<script>
    // Auto-generate slug from title
    document.getElementById('title')?.addEventListener('input', function (e) {
        const slugInput = document.getElementById('slug');
        if (!slugInput.dataset.manual) {
            slugInput.value = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9\s\-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
        }
    });

    document.getElementById('slug')?.addEventListener('input', function () {
        this.dataset.manual = 'true';
    });
</script>

<?php
$childView = ob_get_clean();
require __DIR__ . '/layout.php';
