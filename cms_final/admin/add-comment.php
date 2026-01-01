<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']);
    $author = trim($_POST['author']);
    $content = trim($_POST['content']);
    
    if ($post_id && $author && $content) {
        add_comment($post_id, $author, $content);
    }
    
    header("Location: /post.php?id=$post_id");
    exit;
}
