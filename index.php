<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

if (strpos($uri, 'api/') === 0) {
    require __DIR__ . '/backend/api.php';
    exit();
}

if ($uri === 'admin' || strpos($uri, 'admin/') === 0) {
    require __DIR__ . '/admin/index.php';
    exit();
}

if ($uri === 'property') {
    require __DIR__ . '/frontend/property.php';
    exit();
}

if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico)$/', $uri)) {
    $file = __DIR__ . '/' . $uri;
    if (file_exists($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $contentTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon'
        ];
        header('Content-Type: ' . ($contentTypes[$ext] ?? 'application/octet-stream'));
        readfile($file);
        exit();
    }
}

require __DIR__ . '/frontend/index.php';
