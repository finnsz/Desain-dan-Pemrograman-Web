<?php
$host     = "localhost";
$port     = "5432"; // Port bawaan PostgreSQL
$dbname   = "simpus_mini";
$user     = "postgres"; // Username PostgreSQL kamu
$password = "123"; // Password PostgreSQL kamu

try {
    // Penggunaan driver pgsql untuk PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>