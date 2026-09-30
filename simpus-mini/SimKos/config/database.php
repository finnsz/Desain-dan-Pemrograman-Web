<?php
// Ambil DATABASE_URL dari Environment Variable Vercel
$db_url = getenv('DATABASE_URL');

if ($db_url) {
    // Parser URL Supabase / Cloud Postgres
    $dbopts = parse_url($db_url);
    
    $host     = $dbopts["host"] ?? "localhost";
    $port     = $dbopts["port"] ?? "5432";
    $user     = $dbopts["user"] ?? "postgres";
    $password = $dbopts["pass"] ?? "";
    $dbname   = ltrim($dbopts["path"] ?? "", '/');

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Koneksi Supabase Gagal: " . $e->getMessage());
    }
} else {
    // Fallback jika DATABASE_URL di Vercel belum tersetup
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
        die("Koneksi Database Gagal (DATABASE_URL belum diatur): " . $e->getMessage());
    }
}
?>