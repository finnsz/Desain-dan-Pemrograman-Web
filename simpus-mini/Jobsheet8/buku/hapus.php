<?php
session_start();
require_once '../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM buku WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['flash_message'] = "Buku berhasil dihapus!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Gagal menghapus buku.";
        $_SESSION['flash_type'] = "danger";
    }
}

header("Location: list.php");
exit;