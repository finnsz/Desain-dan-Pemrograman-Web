<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM kamar WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['flash_message'] = "Kamar berhasil dihapus!";
        $_SESSION['flash_type']    = "success";
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Gagal menghapus kamar (mungkin kamar sedang digunakan penghuni): " . $e->getMessage();
        $_SESSION['flash_type']    = "danger";
    }
}

header("Location: list.php");
exit;