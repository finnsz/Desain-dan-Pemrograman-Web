<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/database.php';

csrf_verify();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $no_hp = trim($_POST['no_hp']);
    $kamar_id_baru = empty($_POST['kamar_id']) ? null : (int)$_POST['kamar_id'];
    $tgl_masuk = $_POST['tgl_masuk'];

    try {
        $pdo->beginTransaction();

        // Ambil kamar_id lama
        $stmtOld = $pdo->prepare("SELECT kamar_id FROM penghuni WHERE id = :id");
        $stmtOld->execute(['id' => $id]);
        $kamar_id_lama = $stmtOld->fetchColumn();

        // Update penghuni
        $stmt = $pdo->prepare("UPDATE penghuni SET nama_lengkap = :nama, no_hp = :no_hp, kamar_id = :kamar_id, tgl_masuk = :tgl_masuk WHERE id = :id");
        $stmt->execute([
            'nama' => $nama_lengkap,
            'no_hp' => $no_hp,
            'kamar_id' => $kamar_id_baru,
            'tgl_masuk' => $tgl_masuk,
            'id' => $id
        ]);

        // Update status kamar lama jadi KOSONG (jika ada dan berbeda)
        if ($kamar_id_lama && $kamar_id_lama != $kamar_id_baru) {
            $stmtOldRoom = $pdo->prepare("UPDATE kamar SET status = 'KOSONG' WHERE id = :id");
            $stmtOldRoom->execute(['id' => $kamar_id_lama]);
        }

        // Update status kamar baru jadi TERISI (jika ada)
        if ($kamar_id_baru) {
            $stmtNewRoom = $pdo->prepare("UPDATE kamar SET status = 'TERISI' WHERE id = :id");
            $stmtNewRoom->execute(['id' => $kamar_id_baru]);
        }

        $pdo->commit();

        $_SESSION['flash_message'] = "Data penghuni berhasil diupdate!";
        $_SESSION['flash_type'] = "success";
        header('Location: list.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_message'] = "Gagal update: " . $e->getMessage();
        $_SESSION['flash_type'] = "danger";
        header('Location: list.php');
        exit;
    }
}
?>