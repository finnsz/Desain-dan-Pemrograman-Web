<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = $_POST['id'] ?? '';
    $nomor_kamar = trim($_POST['nomor_kamar'] ?? '');
    $tipe        = trim($_POST['tipe'] ?? '');
    $harga       = trim($_POST['harga'] ?? '');
    $status      = trim($_POST['status'] ?? '');

    if (empty($id) || empty($nomor_kamar) || empty($tipe) || empty($harga) || empty($status)) {
        $_SESSION['flash_message'] = "Semua field wajib diisi!";
        $_SESSION['flash_type'] = "danger";
        header("Location: edit.php?id=" . $id);
        exit;
    }

    try {
        $sql = "UPDATE kamar SET nomor_kamar = :nomor_kamar, tipe = :tipe, harga = :harga, status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nomor_kamar' => $nomor_kamar,
            ':tipe'        => $tipe,
            ':harga'       => (float)$harga,
            ':status'      => $status,
            ':id'          => $id
        ]);

        $_SESSION['flash_message'] = "Data kamar berhasil diperbarui!";
        $_SESSION['flash_type']    = "success";
        header("Location: list.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Gagal memperbarui kamar: " . $e->getMessage();
        $_SESSION['flash_type']    = "danger";
        header("Location: edit.php?id=" . $id);
        exit;
    }
}