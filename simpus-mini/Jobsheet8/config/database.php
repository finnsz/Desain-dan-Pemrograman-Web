<?php
$host     = getenv('DB_HOST') ?: "localhost";
$port     = getenv('DB_PORT') ?: "5432";
$dbname   = getenv('DB_NAME') ?: "simpus_mini";
$user     = getenv('DB_USER') ?: "postgres";
$password = getenv('DB_PASSWORD') ?: "123";

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