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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Install Logos CMS</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            max-width: 500px;
            padding-top: 5vh;
        }

        h1 {
            margin-bottom: 2rem;
        }

        .install-card {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            background: #ffebee;
            color: #c62828;
        }
    </style>
</head>

<body>
    <header>
        <h1>Logos Installer</h1>
        <p>Welcome to your new minimalist blog.</p>
    </header>

    <main>
        <?php if ($message): ?>
            <div class="alert">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="install-card">
            <h3>Database Connection</h3>
            <label>Host <input type="text" name="db_host" value="localhost" required></label>
            <label>Database Name <input type="text" name="db_name" required placeholder="logos_blog"></label>
            <label>User <input type="text" name="db_user" required placeholder="root"></label>
            <label>Password <input type="password" name="db_pass"></label>

            <h3>Admin Account</h3>
            <label>Email (Login) <input type="email" name="admin_email" required></label>
            <label>Password <input type="password" name="admin_pass" required></label>

            <h3>Site Details</h3>
            <label>Site Title <input type="text" name="site_title" value="My Awesome Blog"></label>

            <button type="submit" class="btn-submit" style="width: 100%; margin-top: 1rem;">Install Now</button>
        </form>
    </main>
</body>

</html>