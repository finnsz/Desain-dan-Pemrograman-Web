<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $pdo->beginTransaction();

        // 1. Cari tahu ID kamar yang digunakan penghuni
        $stmt = $pdo->prepare("SELECT kamar_id FROM penghuni WHERE id = ?");
        $stmt->execute([$id]);
        $kamar_id = $stmt->fetchColumn();

        // 2. Hapus penghuni dari database
        $stmtDel = $pdo->prepare("DELETE FROM penghuni WHERE id = ?");
        $stmtDel->execute([$id]);

        // 3. Jika ada kamar terkait, ubah status kamar tersebut menjadi 'KOSONG'
        if ($kamar_id) {
            $stmtKamar = $pdo->prepare("UPDATE kamar SET status = 'KOSONG' WHERE id = ?");
            $stmtKamar->execute([$kamar_id]);
        }

        $pdo->commit();

        $_SESSION['flash_message'] = "Penghuni berhasil dihapus dan kamar terkait kini KOSONG!";
        $_SESSION['flash_type']    = "success";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_message'] = "Gagal menghapus penghuni: " . $e->getMessage();
        $_SESSION['flash_type']    = "danger";
    }
}

header("Location: list.php");
exit;