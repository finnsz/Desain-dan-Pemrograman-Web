<?php
$host     = getenv('host') ?: "localhost";
$port     = getenv('port') ?: "5432";
$dbname   = getenv('database') ?: "postgres";
$user     = getenv('user') ?: "postgres";
$password = getenv('password') ?: "123";

try {
    // sslmode=require wajib untuk jaringan Supabase Transaction Pooler
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>