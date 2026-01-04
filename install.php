<?php
// install.php - One-Click Installer for Logos CMS

// Prevent access if installed
if (file_exists(__DIR__ . '/src/Config/db.php')) {
    header('Location: /');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbName = $_POST['db_name'] ?? '';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_pass'] ?? '';

    $adminEmail = $_POST['admin_email'] ?? '';
    $adminPass = $_POST['admin_pass'] ?? '';
    $siteTitle = $_POST['site_title'] ?? 'My Blog';

    try {
        // 1. Check Connection
        $dsn = "mysql:host=$dbHost;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // Create Database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbName`");

        // 2. Import SQL Logic
        $sqlPath = __DIR__ . '/storage/database.sql';
        if (!file_exists($sqlPath)) {
            throw new Exception("Schema file not found: $sqlPath");
        }
        $sql = file_get_contents($sqlPath);

        // Disable foreign keys for import
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $queries = explode(';', $sql);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        // 3. Create Admin User
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, 'admin')");
        $hash = password_hash($adminPass, PASSWORD_DEFAULT);
        $stmt->execute([$adminEmail, $hash]);

        // 4. Update Settings
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
        $stmt->execute(['site_title', $siteTitle]);

        // 5. Write Config File
        $configDir = __DIR__ . '/src/Config';
        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }

        $configContent = "<?php\n\nreturn [\n" .
            "    'host' => '" . addslashes($dbHost) . "',\n" .
            "    'dbname' => '" . addslashes($dbName) . "',\n" .
            "    'user' => '" . addslashes($dbUser) . "',\n" .
            "    'pass' => '" . addslashes($dbPass) . "',\n" .
            "];\n";

        if (!file_put_contents(__DIR__ . '/src/Config/db.php', $configContent)) {
            throw new Exception("Could not write src/Config/db.php");
        }

        // Success redirect
        header("Location: /?installed=true");
        exit;

    } catch (Exception $e) {
        $message = "Installation Error: " . $e->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Встановлення Logos CMS</title>
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/theme.css">
</head>

<body>
    <header>
        <h1>/\ogos</h1>
        <p>Мінімалістична CMS для вашого блогу</p>
    </header>

    <main>
        <div class="settings-container" style="max-width: 600px; margin: 0 auto;">
            <h2>Встановлення</h2>

            <?php if ($message): ?>
                <div class="alert alert-error" style="margin-bottom: 2rem;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="settings-form">
                <h3>База даних</h3>

                <div class="form-group">
                    <label for="db_host">Хост</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                    <small>Зазвичай localhost або 127.0.0.1</small>
                </div>

                <div class="form-group">
                    <label for="db_name">Назва бази даних</label>
                    <input type="text" id="db_name" name="db_name" required placeholder="logos_blog">
                </div>

                <div class="form-group">
                    <label for="db_user">Користувач</label>
                    <input type="text" id="db_user" name="db_user" required placeholder="root">
                </div>

                <div class="form-group">
                    <label for="db_pass">Пароль</label>
                    <input type="password" id="db_pass" name="db_pass" placeholder="Залиште порожнім, якщо немає">
                </div>

                <h3 style="margin-top: 2rem;">Адміністратор</h3>

                <div class="form-group">
                    <label for="admin_email">Email (для входу)</label>
                    <input type="email" id="admin_email" name="admin_email" required placeholder="admin@example.com">
                </div>

                <div class="form-group">
                    <label for="admin_pass">Пароль</label>
                    <input type="password" id="admin_pass" name="admin_pass" required minlength="3">
                    <small>Мінімум 3 символи</small>
                </div>

                <h3 style="margin-top: 2rem;">Налаштування сайту</h3>

                <div class="form-group">
                    <label for="site_title">Назва блогу</label>
                    <input type="text" id="site_title" name="site_title" value="/\ogos" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Встановити</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>© Logos CMS · Мінімалістична платформа для блогів</p>
    </footer>
</body>

</html>