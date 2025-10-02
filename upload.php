<?php
require_once __DIR__ . '/backend/database.php';

header('Content-Type: text/html; charset=utf-8');

function displayMessage($message, $isError = false) {
    $color = $isError ? '#f44336' : '#4CAF50';
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Upload Result</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
            .message { padding: 20px; border-radius: 4px; background: {$color}; color: white; margin-bottom: 20px; }
            a { color: #1976D2; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class='message'>{$message}</div>
        <a href='index.php'>&larr; Back to Upload Form</a>
    </body>
    </html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    displayMessage('Invalid request method. Please use the upload form.', true);
}

if (!isset($_POST['title']) || empty(trim($_POST['title']))) {
    displayMessage('Image title is required.', true);
}

if (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
    displayMessage('No file was uploaded or an upload error occurred.', true);
}

$title = trim($_POST['title']);
$file = $_FILES['image_file'];

$allowedTypes = ['image/jpeg', 'image/png'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    displayMessage('Invalid file type. Only JPEG and PNG images are allowed.', true);
}

$uploadDir = __DIR__ . '/public/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$extension = ($mimeType === 'image/jpeg') ? 'jpg' : 'png';
$fileName = uniqid('img_') . '.' . $extension;
$filePath = $uploadDir . $fileName;

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    displayMessage('Failed to save the uploaded file.', true);
}

$relativeFilePath = '/public/uploads/' . $fileName;
$fileSize = filesize($filePath);

try {
    $db = Database::getInstance();
    
    $sql = "INSERT INTO images (title, file_path, file_size) VALUES (?, ?, ?)";
    $db->query($sql, [$title, $relativeFilePath, $fileSize]);
    
    $imageId = $db->lastInsertId();
    
    displayMessage("✅ Image uploaded successfully!<br><br>
        <strong>Details:</strong><br>
        - ID: {$imageId}<br>
        - Title: " . htmlspecialchars($title) . "<br>
        - File: {$fileName}<br>
        - Size: " . number_format($fileSize / 1024, 2) . " KB");
    
} catch (Exception $e) {
    unlink($filePath);
    displayMessage('Database error: ' . htmlspecialchars($e->getMessage()), true);
}
