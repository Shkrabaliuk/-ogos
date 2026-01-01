<?php
/**
 * Скрипт для инициализации таблиц Rose Search
 * Выполняет создание необходимых таблиц в базе данных
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/autoload.php';

use S2\Rose\Storage\Database\MysqlRepository;
use S2\Rose\Storage\Database\PdoStorage;

try {
    // Создаем экземпляр MysqlRepository и вызываем метод erase() для создания таблиц
    $repository = new MysqlRepository($pdo, 'rose_');
    $repository->erase();
    
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