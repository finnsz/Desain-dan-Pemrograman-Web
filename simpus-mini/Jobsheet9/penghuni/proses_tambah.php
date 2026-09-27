<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $no_hp        = trim($_POST['no_hp'] ?? '');
    $kamar_id     = trim($_POST['kamar_id'] ?? '');

    if (empty($nama_lengkap) || empty($no_hp) || empty($kamar_id)) {
        $_SESSION['flash_message'] = "Semua field wajib diisi!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Query insert menggunakan kolom nama_lengkap dan kamar_id
        $stmtInsert = $pdo->prepare("INSERT INTO penghuni (nama_lengkap, no_hp, kamar_id) VALUES (?, ?, ?)");
        $stmtInsert->execute([$nama_lengkap, $no_hp, $kamar_id]);

        // Update status kamar menjadi TERISI
        $stmtUpdate = $pdo->prepare("UPDATE kamar SET status = 'TERISI' WHERE id = ?");
        $stmtUpdate->execute([$kamar_id]);

        $pdo->commit();

        $_SESSION['flash_message'] = "Penghuni berhasil ditambahkan!";
        $_SESSION['flash_type']    = "success";
        header("Location: list.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_message'] = "Gagal memproses data: " . $e->getMessage();
        $_SESSION['flash_type']    = "danger";
        header("Location: tambah.php");
        exit;
    }
}