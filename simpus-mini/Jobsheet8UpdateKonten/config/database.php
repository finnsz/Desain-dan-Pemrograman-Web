<?php
// Ambil DATABASE_URL dari Environment Variable Vercel / server
$db_url = getenv('DATABASE_URL');

if ($db_url) {
    // Parser URL Supabase
    $dbopts = parse_url($db_url);
    
    $host = $dbopts["host"];
    $port = isset($dbopts["port"]) ? $dbopts["port"] : "5432";
    $user = $dbopts["user"];
    $password = $dbopts["pass"];
    $dbname = ltrim($dbopts["path"], '/');

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
        $conn = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO_ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Koneksi Supabase Gagal: " . $e->getMessage());
    }
} else {
    // Fallback untuk testing lokal di komputer kamu (jika ada)
    $host = 'localhost';
    $user = 'postgres';
    $password = 'password_lokal';
    $dbname = 'nama_db_lokal';

    try {
        $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    } catch (PDOException $e) {
        die("Koneksi Lokal Gagal: " . $e->getMessage());
    }
}
?>