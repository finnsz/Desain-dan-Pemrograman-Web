<?php
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parseUrl   = parse_url($requestUri, PHP_URL_PATH);

// Tentukan ROOT direktori (naik 1 tingkat dari folder 'api')
$rootDir = dirname(__DIR__);

// 1. Jika membuka domain utama, arahkan ke halaman dashboard
if ($parseUrl === '/' || $parseUrl === '') {
    $parseUrl = '/index.php';
}

$targetFile = $rootDir . $parseUrl;

// 2. Jika URL mengakses sebuah folder (misal /Jobsheet9/), otomatis cari index.php atau index.html
if (is_dir($targetFile)) {
    if (file_exists(rtrim($targetFile, '/') . '/index.php')) {
        $targetFile = rtrim($targetFile, '/') . '/index.php';
    } elseif (file_exists(rtrim($targetFile, '/') . '/index.html')) {
        $targetFile = rtrim($targetFile, '/') . '/index.html';
    }
}

// 3. Jika file ditemukan di server Vercel
if (file_exists($targetFile) && is_file($targetFile)) {
    $ext = pathinfo($targetFile, PATHINFO_EXTENSION);
    
    // Tipe file statis
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg'  => 'image/svg+xml',
        'html' => 'text/html'
    ];

    if ($ext === 'php') {
        require $targetFile;
        exit;
    } elseif (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($targetFile);
        exit;
    }
}

// 4. JIKA TETAP 404, TAMPILKAN DEBUGGER INI (Untuk mencari tahu penyebabnya)
http_response_code(404);
echo "<div style='font-family: sans-serif; padding: 20px;'>";
echo "<h2 style='color: red;'>404 Not Found</h2>";
echo "<p>File atau URL yang kamu minta tidak ditemukan.</p>";
echo "<hr>";
echo "<p><b>URL dipanggil:</b> " . htmlspecialchars($parseUrl) . "</p>";
echo "<p><b>Server mencari di path:</b> " . htmlspecialchars($targetFile) . "</p>";
echo "<hr>";

// --- TAMBAHAN DEBUG: tampilkan isi folder asli di server ---
echo "<p><b>Isi \$rootDir ($rootDir):</b></p>";
if (is_dir($rootDir)) {
    echo "<pre>" . htmlspecialchars(print_r(scandir($rootDir), true)) . "</pre>";
} else {
    echo "<p style='color:red'>\$rootDir bahkan tidak ditemukan sebagai folder!</p>";
}

$jobsheetPath = $rootDir . '/Jobsheet9';
echo "<p><b>Isi $jobsheetPath:</b></p>";
if (is_dir($jobsheetPath)) {
    echo "<pre>" . htmlspecialchars(print_r(scandir($jobsheetPath), true)) . "</pre>";
} else {
    echo "<p style='color:red'>Folder Jobsheet9 TIDAK ditemukan di server pada path ini.</p>";
}
// --- AKHIR TAMBAHAN ---

echo "</div>";
?>