<?php
// Ambil variabel dari Vercel / Environment, atau gunakan default jika di lokal
$host     = getenv('host') ?: "localhost";
$port     = getenv('port') ?: "5432";
$dbname   = getenv('database') ?: "simpus_mini";
$user     = getenv('user') ?: "postgres";
$password = getenv('password') ?: "123";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>