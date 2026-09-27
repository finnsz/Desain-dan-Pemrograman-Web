<?php
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE penghuni SET nama_lengkap = :nama, no_hp = :no_hp, kamar_id = :kamar_id, tgl_masuk = :tgl_masuk WHERE id = :id");
    $stmt->execute([
        'nama' => $_POST['nama_lengkap'],
        'no_hp' => $_POST['no_hp'],
        'kamar_id' => empty($_POST['kamar_id']) ? null : $_POST['kamar_id'],
        'tgl_masuk' => $_POST['tgl_masuk'],
        'id' => $_POST['id']
    ]);
    header('Location: list.php');
}
?>