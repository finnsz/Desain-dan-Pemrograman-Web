<?php
// kamar/proses_tambah.php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_kamar = trim($_POST['nomor_kamar'] ?? '');
    $tipe        = trim($_POST['tipe'] ?? '');
    $harga       = trim($_POST['harga'] ?? '');

    if (empty($nomor_kamar) || empty($tipe) || empty($harga)) {
        $_SESSION['flash_message'] = "Semua field wajib diisi!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    $sql = "INSERT INTO kamar (nomor_kamar, tipe, harga) VALUES (:nomor_kamar, :tipe, :harga)";
    $stmt = $pdo->prepare($sql);
    $simpan = $stmt->execute([
        ':nomor_kamar' => $nomor_kamar,
        ':tipe'        => $tipe,
        ':harga'       => (float)$harga
    ]);

    if ($simpan) {
        $_SESSION['flash_message'] = "Kamar berhasil ditambahkan!";
        $_SESSION['flash_type']    = "success";
    }

    header("Location: list.php");
    exit;
}
?>