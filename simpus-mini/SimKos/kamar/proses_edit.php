<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/database.php';

csrf_verify();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE kamar SET nomor_kamar = :nomor, tipe = :tipe, harga = :harga, status = :status WHERE id = :id");
    $stmt->execute([
        'nomor' => $_POST['nomor_kamar'],
        'tipe' => $_POST['tipe'],
        'harga' => $_POST['harga'],
        'status' => $_POST['status'],
        'id' => $_POST['id']
    ]);
    header('Location: list.php');
}
?>