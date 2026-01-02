<?php
/**
 * Сторінка входу для адміністратора
 * Мінімалістичний дизайн без зайвих елементів
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

// Якщо вже авторизований - редірект на головну
if (isLoggedIn()) {
    header('Location: /');
    exit;
}

$error = null;

// Обробка форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Заповніть всі поля';
    } else {
        $result = attemptLogin($pdo, $username, $password);
        
        if ($result['success']) {
            // Успішний вхід - редірект на головну або на попередню сторінку
            $redirect = $_GET['redirect'] ?? '/';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = $result['error'];
        }
    }
}

$pageTitle = 'Вхід';
$content = '';
ob_start();
?>

<div class="login-container">
    <div class="login-box">
        <h1 class="login-title">/\ogos</h1>
        <p class="login-subtitle">Адміністрування</p>
        
        <?php if ($error): ?>
            <div class="login-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Ім'я користувача</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autofocus
                    autocomplete="username"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="password">Пароль</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    autocomplete="current-password"
                >
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-unlock"></i>
                Увійти
            </button>
        </form>
        
        <div class="login-footer">
            <a href="/" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Повернутися на головну
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/views/layout.php';
