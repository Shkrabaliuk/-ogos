<?php
namespace App\Controllers\Admin;

use App\Config\Database;
use App\Services\Auth;

class BackupController
{
    private $pdo;

    public function __construct()
    {
        Auth::require();
        $this->pdo = Database::connect();
    }

    public function download()
    {
        $backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backupDir = __DIR__ . '/../../../storage/backups';

        // Create directory if not exists
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $backupPath = $backupDir . '/' . $backupFile;

        try {
            // Get all tables
            $tables = [];
            $result = $this->pdo->query("SHOW TABLES");
            while ($row = $result->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            // Get database name from config
            $config = require __DIR__ . '/../../Config/db.php';
            $dbName = $config['dbname'] ?? 'unknown';

            $sqlDump = "-- Database Backup\n";
            $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sqlDump .= "-- Database: " . $dbName . "\n\n";
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            // For each table
            foreach ($tables as $table) {
                // DROP TABLE
                $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";

                // CREATE TABLE
                $createTable = $this->pdo->query("SHOW CREATE TABLE `$table`")->fetch(\PDO::FETCH_NUM);
                $sqlDump .= $createTable[1] . ";\n\n";

                // INSERT DATA
                $rows = $this->pdo->query("SELECT * FROM `$table`")->fetchAll(\PDO::FETCH_ASSOC);

                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $values = array_map(function ($value) {
                            return $value === null ? 'NULL' : $this->pdo->quote($value);
                        }, array_values($row));

                        $sqlDump .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sqlDump .= "\n";
                }
            }

            $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

            // Save to file
            file_put_contents($backupPath, $sqlDump);

            // Send file for download
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="' . $backupFile . '"');
            header('Content-Length: ' . filesize($backupPath));
            header('Pragma: no-cache');
            header('Expires: 0');

            readfile($backupPath);
            exit;

        } catch (\Exception $e) {
            error_log("Backup error: " . $e->getMessage());
            die("Помилка створення backup: " . htmlspecialchars($e->getMessage()));
        }
    }
}
