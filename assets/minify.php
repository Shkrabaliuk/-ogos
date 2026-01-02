<?php
// assets/minify.php - CSS/JS мініфікація з кешуванням

$file = $_GET['f'] ?? '';
$type = $_GET['t'] ?? 'css'; // css або js

if (empty($file)) {
    http_response_code(400);
    die('No file specified');
}

// Безпека: тільки файли з assets/
$basePath = __DIR__;
$allowedDirs = ['css', 'js'];
$filePath = null;

foreach ($allowedDirs as $dir) {
    $testPath = $basePath . '/' . $dir . '/' . basename($file);
    if (file_exists($testPath)) {
        $filePath = $testPath;
        break;
    }
}

if (!$filePath || !file_exists($filePath)) {
    http_response_code(404);
    die('File not found');
}

// Кеш папка
$cacheDir = $basePath . '/cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Ім'я кешованого файлу
$cacheFile = $cacheDir . '/' . md5($file) . '.min.' . $type;

// Перевірка чи кеш актуальний
$needsRebuild = !file_exists($cacheFile) || 
                filemtime($filePath) > filemtime($cacheFile);

if ($needsRebuild) {
    $content = file_get_contents($filePath);
    
    if ($type === 'css') {
        $minified = minifyCSS($content);
        header('Content-Type: text/css; charset=utf-8');
    } else {
        $minified = minifyJS($content);
        header('Content-Type: application/javascript; charset=utf-8');
    }
    
    // Зберігаємо в кеш
    file_put_contents($cacheFile, $minified);
} else {
    $minified = file_get_contents($cacheFile);
    
    if ($type === 'css') {
        header('Content-Type: text/css; charset=utf-8');
    } else {
        header('Content-Type: application/javascript; charset=utf-8');
    }
}

// Кешування в браузері (30 днів)
header('Cache-Control: public, max-age=2592000');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT');

echo $minified;
exit;

/**
 * Мініфікація CSS
 */
function minifyCSS($css) {
    // Видалити коментарі
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Видалити пробіли, табуляції, переноси рядків
    $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
    
    // Видалити зайві пробіли
    $css = preg_replace('/\s+/', ' ', $css);
    
    // Видалити пробіли навколо символів
    $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
    
    // Видалити останню крапку з комою перед }
    $css = str_replace(';}', '}', $css);
    
    return trim($css);
}

/**
 * Мініфікація JS (базова)
 */
function minifyJS($js) {
    // Видалити однолінійні коментарі
    $js = preg_replace('~//.*~m', '', $js);
    
    // Видалити багаторядкові коментарі
    $js = preg_replace('~/\*.*?\*/~s', '', $js);
    
    // Видалити зайві пробіли та переноси
    $js = preg_replace('/\s+/', ' ', $js);
    
    // Видалити пробіли навколо операторів
    $js = preg_replace('/\s*([{}();,:])\s*/', '$1', $js);
    
    return trim($js);
}
