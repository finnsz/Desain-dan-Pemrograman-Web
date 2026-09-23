<?php
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

$rootDir = dirname(__DIR__);

// 1. Penanganan File Statis (CSS, JS, Gambar)
$staticFile = $rootDir . $parseUrl;
if (file_exists($staticFile) && is_file($staticFile)) {
    $ext = pathinfo($staticFile, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg'  => 'image/svg+xml'
    ];

    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($staticFile);
        exit;
    }
}

// 2. Routing URL ke File PHP
if (file_exists($rootDir . $parseUrl) && is_file($rootDir . $parseUrl)) {
    require $rootDir . $parseUrl;
    exit;
}

// Route default jika membuka root domain
if ($parseUrl === '/' || $parseUrl === '') {
    require $rootDir . '/index.php';
    exit;
}

http_response_code(404);
echo "404 Not Found - " . htmlspecialchars($parseUrl);