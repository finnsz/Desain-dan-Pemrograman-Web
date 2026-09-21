<?php
// Router untuk Vercel Serverless Function
$requestUri = $_SERVER['REQUEST_URI'];
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

$file = __DIR__ . '/..' . $parseUrl;

if ($parseUrl === '/' || $parseUrl === '') {
    require __DIR__ . '/../index.php';
    exit;
}

if (file_exists($file) && is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    require $file;
    exit;
}

if (file_exists($file) && is_file($file)) {
    return false;
}

http_response_code(404);
echo "404 Not Found";