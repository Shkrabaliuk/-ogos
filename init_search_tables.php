<?php
/**
 * Скрипт для инициализации таблиц Rose Search
 * Выполняет создание необходимых таблиц в базе данных
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/autoload.php';

try {
    // Отключаем проверку внешних ключей для безопасного удаления таблиц
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    
    // Удаляем существующие таблицы Rose Search, если они есть
    $tables = ['rose_content', 'rose_toc', 'rose_fulltext_index', 'rose_keyword_index'];
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`;");
    }
    
    // Включаем проверку внешних ключей обратно
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    
    // Создаем таблицы Rose Search в правильном порядке (с учетом внешних ключей)
    $pdo->exec("
        -- Таблиця Table of Contents (головна таблиця документів)
        CREATE TABLE IF NOT EXISTS `rose_toc` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `external_id` varchar(255) NOT NULL,
          `title` varchar(255) NOT NULL,
          `description` text,
          `added_at` datetime DEFAULT CURRENT_TIMESTAMP,
          `url` varchar(500) DEFAULT NULL,
          `hash` varchar(80) NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `external_id` (`external_id`),
          KEY `hash` (`hash`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    $pdo->exec("
        -- Таблиця для fulltext індексу (слова з позиціями)
        CREATE TABLE IF NOT EXISTS `rose_fulltext_index` (
          `word` varchar(80) NOT NULL,
          `toc_id` int(11) unsigned NOT NULL,
          `position` int(11) unsigned NOT NULL,
          PRIMARY KEY (`word`, `toc_id`, `position`),
          KEY `toc_id` (`toc_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    $pdo->exec("
        -- Таблиця для keywords індексу (заголовки, теги)
        CREATE TABLE IF NOT EXISTS `rose_keyword_index` (
          `keyword` varchar(255) NOT NULL,
          `toc_id` int(11) unsigned NOT NULL,
          `type` tinyint(1) unsigned NOT NULL,
          PRIMARY KEY (`keyword`, `toc_id`),
          KEY `toc_id` (`toc_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    $pdo->exec("
        -- Таблиця для зберігання контенту (для генерації сніпетів)
        CREATE TABLE IF NOT EXISTS `rose_content` (
          `toc_id` int(11) unsigned NOT NULL,
          `content` longtext NOT NULL,
          PRIMARY KEY (`toc_id`),
          CONSTRAINT `fk_rose_content_toc` FOREIGN KEY (`toc_id`) REFERENCES `rose_toc` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    echo "Таблицы Rose Search успешно созданы!\n";
    
    // Также можно сразу запустить переиндексацию
    if (file_exists('reindex.php')) {
        echo "Запуск переиндексации...\n";
        include 'reindex.php';
        echo "Переиндексация завершена!\n";
    }
} catch (Exception $e) {
    echo "Ошибка при создании таблиц Rose Search: " . $e->getMessage() . "\n";
    exit(1);
}