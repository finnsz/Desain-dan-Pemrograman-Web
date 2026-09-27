<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = $_POST['id'] ?? '';
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $no_hp        = trim($_POST['no_hp'] ?? '');
    $kamar_id_baru = trim($_POST['kamar_id'] ?? '');

    if (empty($id) || empty($nama_lengkap) || empty($no_hp) || empty($kamar_id_baru)) {
        $_SESSION['flash_message'] = "Semua field wajib diisi!";
        $_SESSION['flash_type'] = "danger";
        header("Location: edit.php?id=" . $id);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Ambil kamar lama penghuni
        $stmtOld = $pdo->prepare("SELECT kamar_id FROM penghuni WHERE id = ?");
        $stmtOld->execute([$id]);
        $kamar_id_lama = $stmtOld->fetchColumn();

        // 2. Jika pindah kamar, kosongkan kamar lama
        if ($kamar_id_lama && $kamar_id_lama != $kamar_id_baru) {
            $stmtReset = $pdo->prepare("UPDATE kamar SET status = 'KOSONG' WHERE id = ?");
            $stmtReset->execute([$kamar_id_lama]);
        }

        // 3. Update data penghuni
        $stmtUpdate = $pdo->prepare("UPDATE penghuni SET nama_lengkap = ?, no_hp = ?, kamar_id = ? WHERE id = ?");
        $stmtUpdate->execute([$nama_lengkap, $no_hp, $kamar_id_baru, $id]);

        // 4. Set kamar baru menjadi TERISI
        $stmtSetOccupied = $pdo->prepare("UPDATE kamar SET status = 'TERISI' WHERE id = ?");
        $stmtSetOccupied->execute([$kamar_id_baru]);

        $pdo->commit();

        $_SESSION['flash_message'] = "Data penghuni berhasil diperbarui!";
        $_SESSION['flash_type']    = "success";
        header("Location: list.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_message'] = "Gagal memproses perubahan: " . $e->getMessage();
        $_SESSION['flash_type']    = "danger";
        header("Location: edit.php?id=" . $id);
        exit;
    }
}