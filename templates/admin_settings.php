<?php
/**
 * Admin Settings View
 * Grid Layout - Compact & Visible
 */

use App\Services\View;
use App\Services\Csrf;

// Get success/error messages from session
$success = $_SESSION['settings_success'] ?? null;
$error = $_SESSION['settings_error'] ?? null;
unset($_SESSION['settings_success'], $_SESSION['settings_error']);

// Load current settings
$stmt = $this->pdo->query("SELECT `key`, `value` FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['key']] = $row['value'];
}

$blogTitle = $settings['site_title'] ?? '/\\ogos';
$pageTitle = "Налаштування — {$blogTitle}";
$isAdmin = true;

ob_start();
?>

<style>
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        align-items: start;
    }

    .settings-card {
        background: var(--bg-color, #fff);
        border: 1px solid var(--border-color, #eee);
        padding: 1.5rem;
        border-radius: 8px;
    }

    .settings-card h2 {
        margin-top: 0;
        margin-bottom: 1rem;
        font-size: 1.25rem;
        border-bottom: 1px solid var(--border-color, #eee);
        padding-bottom: 0.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.25rem;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 0.5rem;
        font-size: 0.95rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .form-group small {
        display: block;
        margin-top: 0.25rem;
        color: #777;
        font-size: 0.8rem;
    }

    .btn-submit {
        width: 100%;
        margin-top: 1rem;
    }
</style>

<div class="settings-container" style="max-width: 1200px;">
    <h1 style="margin-bottom: 1.5rem;">Налаштування</h1>

    <?php if ($success): ?>
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="settings-grid">

        <!-- Column 1: General & Author -->
        <div>
            <form method="POST" action="/admin/settings" class="settings-form">
                <input type="hidden" name="csrf_token" value="<?= Csrf::generate() ?>">

                <!-- General Settings Card -->
                <div class="settings-card" style="margin-bottom: 1.5rem;">
                    <h2>Загальні</h2>

                    <div class="form-group">
                        <label for="blog_title">Назва блогу</label>
                        <input type="text" id="blog_title" name="blog_title"
                            value="<?= htmlspecialchars($settings['site_title'] ?? '/\\ogos') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="blog_tagline">Підзаголовок</label>
                        <input type="text" id="blog_tagline" name="blog_tagline"
                            value="<?= htmlspecialchars($settings['blog_tagline'] ?? '') ?>">
                        <small>Короткий опис під логотипом</small>
                    </div>

                    <div class="form-group">
                        <label for="posts_per_page">Постів на сторінку</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="number" id="posts_per_page" name="posts_per_page"
                                value="<?= (int) ($settings['posts_per_page'] ?? 10) ?>" min="1" max="50" required
                                style="width: 80px;">
                            <div style="line-height: 2.2rem; font-size: 0.9rem; color: #777;">постів</div>
                        </div>
                    </div>
                </div>

                <!-- Author & SEO Card -->
                <div class="settings-card">
                    <h2>Автор та SEO</h2>

                    <div class="form-group">
                        <label for="blog_author">Ім'я автора</label>
                        <input type="text" id="blog_author" name="blog_author"
                            value="<?= htmlspecialchars($settings['blog_author'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="author_avatar">Аватар</label>
                        <input type="hidden" id="author_avatar" name="author_avatar"
                            value="<?= htmlspecialchars($settings['author_avatar'] ?? '') ?>">

                        <div class="avatar-upload-container"
                            style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
                            <!-- Preview -->
                            <div class="avatar-preview"
                                style="width: 64px; height: 64px; border-radius: 50%; background: #eee; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd;">
                                <?php if (!empty($settings['author_avatar'])): ?>
                                    <img src="<?= htmlspecialchars($settings['author_avatar']) ?>" id="avatarPreviewImg"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <img src="" id="avatarPreviewImg"
                                        style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                    <span id="avatarPlaceholder" style="font-size: 2rem; color: #ccc;">👤</span>
                                <?php endif; ?>
                            </div>

                            <!-- Controls -->
                            <div class="avatar-controls">
                                <input type="file" id="avatarUploadInput" accept="image/*" style="display: none;">
                                <button type="button" class="btn-secondary" id="btnUploadAvatar"
                                    style="padding: 0.4rem 0.8rem; font-size: 0.9rem;">
                                    Завантажити фото
                                </button>
                                <span id="uploadStatus"
                                    style="font-size: 0.85rem; margin-left: 0.5rem; color: #666;"></span>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const btnUpload = document.getElementById('btnUploadAvatar');
                            const fileInput = document.getElementById('avatarUploadInput');
                            const hiddenInput = document.getElementById('author_avatar');
                            const previewImg = document.getElementById('avatarPreviewImg');
                            const placeholder = document.getElementById('avatarPlaceholder');
                            const status = document.getElementById('uploadStatus');

                            btnUpload.addEventListener('click', () => fileInput.click());

                            fileInput.addEventListener('change', () => {
                                if (fileInput.files.length === 0) return;

                                const file = fileInput.files[0];
                                const formData = new FormData();
                                formData.append('image', file);

                                status.textContent = 'Завантаження...';
                                btnUpload.disabled = true;

                                fetch('/admin/upload_image', {
                                    method: 'POST',
                                    body: formData
                                })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.success) {
                                            // Update Hidden Input
                                            hiddenInput.value = data.url;

                                            // Update Preview
                                            previewImg.src = data.url;
                                            previewImg.style.display = 'block';
                                            if (placeholder) placeholder.style.display = 'none';

                                            status.textContent = 'Готово!';
                                            status.style.color = 'green';
                                        } else {
                                            status.textContent = 'Помилка: ' + (data.error || 'Невідома');
                                            status.style.color = 'red';
                                        }
                                    })
                                    .catch(err => {
                                        console.error(err);
                                        status.textContent = 'Помилка мережі';
                                        status.style.color = 'red';
                                    })
                                    .finally(() => {
                                        btnUpload.disabled = false;
                                        fileInput.value = ''; // Reset input to allow re-upload same file
                                    });
                            });
                        });
                    </script>

                    <div class="form-group">
                        <label for="blog_description">Meta Description</label>
                        <textarea id="blog_description" name="blog_description" rows="3"
                            maxlength="300"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="google_analytics_id">Google Analytics ID</label>
                        <input type="text" id="google_analytics_id" name="google_analytics_id"
                            value="<?= htmlspecialchars($settings['google_analytics_id'] ?? '') ?>"
                            placeholder="G-XXXXXXXXXX">
                    </div>

                    <button type="submit" class="btn-submit">Зберегти налаштування</button>
                </div>
            </form>
        </div>

        <!-- Column 2: Security & System -->
        <div>
            <!-- Password Card -->
            <div class="settings-card" style="margin-bottom: 1.5rem;">
                <h2>Безпека</h2>
                <form method="POST" action="/admin/settings">
                    <input type="hidden" name="csrf_token" value="<?= Csrf::generate() ?>">
                    <input type="hidden" name="change_password" value="1">

                    <div class="form-group">
                        <label for="current_password">Поточний пароль</label>
                        <input type="password" id="current_password" name="current_password" required
                            autocomplete="current-password">
                    </div>

                    <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label for="new_password">Новий пароль</label>
                            <input type="password" id="new_password" name="new_password" required minlength="3"
                                autocomplete="new-password">
                        </div>
                        <div>
                            <label for="confirm_password">Підтвердження</label>
                            <input type="password" id="confirm_password" name="confirm_password" required
                                autocomplete="new-password">
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Змінити пароль</button>
                </form>
            </div>

            <!-- System Card -->
            <div class="settings-card">
                <h2>Система</h2>

                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
                    <div>
                        <strong>Резервна копія</strong>
                        <div style="font-size: 0.8rem; color: #777;">SQL дамп бази даних</div>
                    </div>
                    <a href="/admin/backup" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Завантажити</a>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="margin-bottom: 0.5rem;">
                        <strong style="color: #d00;">Перевстановлення CMS</strong>
                        <div style="font-size: 0.8rem; color: #777;">Видаляє config/db.php</div>
                    </div>
                    <form method="POST" action="/admin/reinstall"
                        onsubmit="return confirm('Ви впевнені? Це видалить конфігурацію БД!');">
                        <input type="hidden" name="csrf_token" value="<?= Csrf::generate() ?>">
                        <button type="submit" class="btn"
                            style="width: 100%; background: white; border: 1px solid #d00; color: #d00;">
                            Перевстановити
                        </button>
                    </form>
                </div>

                <div
                    style="background: #f9f9f9; padding: 0.75rem; border-radius: 4px; font-size: 0.85rem; color: #555;">
                    <div><strong>CMS Version:</strong> 1.0.0</div>
                    <div><strong>PHP:</strong> <?= PHP_VERSION ?></div>
                    <div><strong>Admin:</strong> <?= htmlspecialchars($_SESSION['admin_email'] ?? 'admin') ?></div>
                </div>

                <!-- Logout Button -->
                <div style="margin-top: 1.5rem; text-align: center;">
                    <a href="/api/logout.php" class="btn"
                        style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Вийти з адмінки
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$childView = ob_get_clean();
require __DIR__ . '/layout.php';
