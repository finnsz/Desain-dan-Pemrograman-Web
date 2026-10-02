<?php
require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/role.php';

require_admin('list.php');

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$id) {
    $_SESSION['flash_message'] = 'ID tidak valid.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: list.php');
    exit;
}

try {
    // Ambil file bukti untuk dihapus
    $stmt = $pdo->prepare("SELECT bukti_path FROM pengeluaran WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $bukti_path = $stmt->fetchColumn();

    // Delete dari database
    $delete = $pdo->prepare("DELETE FROM pengeluaran WHERE id = :id");
    $delete->execute([':id' => $id]);

    // Hapus file bukti jika ada
    if ($bukti_path && file_exists(__DIR__ . '/../' . $bukti_path)) {
        unlink(__DIR__ . '/../' . $bukti_path);
    }

    $_SESSION['flash_message'] = 'Data pengeluaran berhasil dihapus!';
    $_SESSION['flash_type'] = 'success';
} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'danger';
}

header('Location: list.php');
exit;
?>
