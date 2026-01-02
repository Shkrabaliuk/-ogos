<?php
// admin/backup.php - Резервне копіювання бази даних

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Перевірка авторизації
requireAuth();

// Налаштування БД (з config/db.php)
$backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
$backupDir = __DIR__ . '/../backups';

// Створюємо папку якщо не існує
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$backupPath = $backupDir . '/' . $backupFile;

try {
    // Отримуємо всі таблиці
    $tables = [];
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    $sqlDump = "-- Database Backup\n";
    $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $sqlDump .= "-- Database: " . $db . "\n\n";
    $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    // Для кожної таблиці
    foreach ($tables as $table) {
        // DROP TABLE
        $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
        
        // CREATE TABLE
        $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
        $sqlDump .= $createTable[1] . ";\n\n";
        
        // INSERT DATA
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $values = array_map(function($value) use ($pdo) {
                    return $value === null ? 'NULL' : $pdo->quote($value);
                }, array_values($row));
                
                $sqlDump .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
            }
            $sqlDump .= "\n";
        }
    }
    
    $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    // Зберігаємо в файл
    file_put_contents($backupPath, $sqlDump);
    
    // Віддаємо файл для завантаження
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $backupFile . '"');
    header('Content-Length: ' . filesize($backupPath));
    header('Pragma: no-cache');
    header('Expires: 0');
    
    readfile($backupPath);
    exit;
    
} catch (Exception $e) {
    error_log("Backup error: " . $e->getMessage());
    die("Помилка створення backup: " . htmlspecialchars($e->getMessage()));
}
