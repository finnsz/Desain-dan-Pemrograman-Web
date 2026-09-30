<?php
// penghuni/get_kamar.php - API untuk fetch kamar kosong berdasarkan tipe
require_once __DIR__ . '/../config/database.php';

$tipe = $_GET['tipe'] ?? '';

if (!$tipe) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, nomor_kamar, tipe, harga, status FROM kamar WHERE tipe = :tipe AND status = 'KOSONG' ORDER BY nomor_kamar ASC");
$stmt->execute(['tipe' => $tipe]);
$kamar = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($kamar);
?>
