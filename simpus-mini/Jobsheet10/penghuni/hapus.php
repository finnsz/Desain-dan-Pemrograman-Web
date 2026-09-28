<?php
require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/role.php';
require_once __DIR__ . '/../config/database.php';

// Hanya admin yang boleh menghapus
require_admin('list.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM penghuni WHERE id = :id");
    $stmt->execute(['id' => $_POST['id'] ?? 0]);

    $_SESSION['flash_message'] = 'Data penghuni berhasil dihapus.';
    $_SESSION['flash_type']    = 'success';
}
header('Location: list.php');
exit;
