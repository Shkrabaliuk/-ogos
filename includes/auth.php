<?php
/**
 * Мінімалістична система авторизації для /\ogos
 * Процедурний стиль, максимальна простота
 */

// Старт сесії, якщо ще не запущено
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Перевірка авторизації
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Встановлення сесії після успішного входу
 * @param int $userId
 * @param string $username
 */
function setAuthSession($userId, $username) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $userId;
    $_SESSION['admin_username'] = $username;
    
    // Регенерація ID сесії для безпеки
    session_regenerate_id(true);
}

/**
 * Знищення сесії (logout)
 */
function destroyAuthSession() {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Перевірка credentials і авторизація
 * @param PDO $pdo
 * @param string $username
 * @param string $password
 * @return array ['success' => bool, 'error' => string|null]
 */
function attemptLogin($pdo, $username, $password) {
    try {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return ['success' => false, 'error' => 'Невірне ім\'я користувача або пароль'];
        }
        
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Невірне ім\'я користувача або пароль'];
        }
        
        // Успішний вхід
        setAuthSession($user['id'], $user['username']);
        
        return ['success' => true, 'error' => null];
        
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'error' => 'Помилка авторизації'];
    }
}

/**
 * Спрощена авторизація тільки за паролем (для одного адміна)
 * @param PDO $pdo
 * @param string $password
 * @return array ['success' => bool, 'error' => string|null]
 */
function attemptLoginWithPassword($pdo, $password) {
    try {
        // Беремо першого (і єдиного) адміна
        $stmt = $pdo->query("SELECT id, username, password_hash FROM admin_users LIMIT 1");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return ['success' => false, 'error' => 'Адміністратор не знайдений'];
        }
        
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Невірний пароль'];
        }
        
        // Успішний вхід
        setAuthSession($user['id'], $user['username']);
        
        return ['success' => true, 'error' => null];
        
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'error' => 'Помилка авторизації'];
    }
}

/**
 * Редірект на login, якщо не авторизований
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}
