<?php
$db_url = getenv('DATABASE_URL');

if ($db_url) {
    $dbopts = parse_url($db_url);
    
    $host     = $dbopts["host"];
    $port     = isset($dbopts["port"]) ? $dbopts["port"] : "5432";
    $user     = $dbopts["user"];
    $password = $dbopts["pass"];
    $dbname   = ltrim($dbopts["path"], '/');

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
        // UBAH $conn MENJADI $pdo DI SINI
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Koneksi Supabase Gagal: " . $e->getMessage());
    }
}
?>