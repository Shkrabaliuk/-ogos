<?php
/**
 * Вихід з системи
 */

require_once __DIR__ . '/includes/auth.php';

destroyAuthSession();

// Редірект на головну
header('Location: /');
exit;
