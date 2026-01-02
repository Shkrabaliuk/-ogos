<?php
/**
 * Сторінка налаштувань блогу
 * Доступна тільки для авторизованих адмінів
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

// Перевіряємо авторизацію
requireAuth();

$success = null;
$error = null;

// Завантажуємо поточні налаштування
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}

// Обробка форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Перевірка CSRF
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Невірний CSRF токен';
    } else {
        try {
            $blogTitle = trim($_POST['blog_title'] ?? '');
            $postsPerPage = (int)($_POST['posts_per_page'] ?? 5);
            
            if (empty($blogTitle)) {
                $error = 'Назва блогу не може бути порожньою';
            } else {
                // Оновлюємо налаштування
                $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?");
                
                $stmt->execute(['blog_title', $blogTitle, $blogTitle]);
                $stmt->execute(['posts_per_page', $postsPerPage, $postsPerPage]);
                
                $settings['blog_title'] = $blogTitle;
                $settings['posts_per_page'] = $postsPerPage;
                
                $success = 'Налаштування збережено';
            }
        } catch (PDOException $e) {
            error_log("Settings update error: " . $e->getMessage());
            $error = 'Помилка збереження налаштувань';
        }
    }
}

$pageTitle = 'Налаштування';
$content = '';
ob_start();
?>

<div class="settings-container">
    <div class="settings-header">
        <h1>Налаштування блогу</h1>
        <a href="/" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            На головну
        </a>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="settings-form">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        
        <div class="form-group">
            <label for="blog_title">Назва блогу</label>
            <input 
                type="text" 
                id="blog_title" 
                name="blog_title" 
                value="<?= htmlspecialchars($settings['blog_title'] ?? '/\\ogos') ?>"
                required
            >
            <small>Відображається в заголовку та логотипі</small>
        </div>
        
        <div class="form-group">
            <label for="posts_per_page">Постів на сторінку</label>
            <input 
                type="number" 
                id="posts_per_page" 
                name="posts_per_page" 
                value="<?= (int)($settings['posts_per_page'] ?? 10) ?>"
                min="1"
                max="50"
                required
            >
            <small>Кількість постів на одній сторінці</small>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i>
                Зберегти налаштування
            </button>
        </div>
    </form>
    
    <div class="settings-info">
        <h2>Інформація</h2>
        <ul>
            <li><strong>Версія:</strong> 1.0.0</li>
            <li><strong>PHP:</strong> <?= PHP_VERSION ?></li>
            <li><strong>Адміністратор:</strong> <?= htmlspecialchars($_SESSION['admin_username']) ?></li>
        </ul>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../views/layout.php';
