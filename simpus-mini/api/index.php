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
echo "<h3>Cek 2 hal ini:</h3>";
echo "<ol>";
echo "<li><b>Huruf Besar/Kecil:</b> Vercel sangat ketat. Pastikan nama folder di repositori GitHub benar-benar persis <b>Jobsheet9</b> (J besar), bukan <b>jobsheet9</b> (j kecil).</li>";
echo "<li><b>Deploy Vercel:</b> Pastikan vercel.json sudah diset <code>\"includeFiles\": \"**\"</code></li>";
echo "</ol></div>";
?>