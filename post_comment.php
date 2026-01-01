<?php
require_once 'config/db.php';
require_once 'includes/csrf.php';
require_once 'includes/CommentHandler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF token validation failed');
    }
    
    $handler = new CommentHandler($pdo);
    
    try {
        $handler->addComment(
            $_POST['post_id'],
            $_POST['author_name'],
            $_POST['content'],
            $_POST['parent_id'] ?? null
        );
        
        header('Location: ' . $_POST['redirect_url']);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>