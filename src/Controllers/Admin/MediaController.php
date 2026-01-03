<?php
namespace App\Controllers\Admin;

use App\Services\Auth;

class MediaController
{
    public function upload()
    {
        // Check authentication
        if (!Auth::check()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // Check method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        // Check file presence
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            exit;
        }

        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

        // Check file type
        if (!in_array($file['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP']);
            exit;
        }

        // Check size (max 10MB)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file['size'] > $maxSize) {
            http_response_code(400);
            echo json_encode(['error' => 'File too large. Maximum 10MB']);
            exit;
        }

        // Create folder structure: uploads/YYYY/MM/
        $year = date('Y');
        $month = date('m');
        $uploadDir = __DIR__ . "/../../../storage/uploads/{$year}/{$month}";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = "{$uploadDir}/{$filename}";

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save file']);
            exit;
        }

        // Return URL for editor insertion
        $url = "/storage/uploads/{$year}/{$month}/{$filename}";

        echo json_encode([
            'success' => true,
            'url' => $url,
            'markdown' => "![{$file['name']}]({$url})",
            'filename' => $filename
        ]);
    }
}
