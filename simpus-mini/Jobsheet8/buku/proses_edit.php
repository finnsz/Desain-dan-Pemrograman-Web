<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = $_POST['id'] ?? '';
    $judul     = trim($_POST['judul'] ?? '');
    $pengarang = trim($_POST['pengarang'] ?? '');
    $tahun     = trim($_POST['tahun'] ?? '');
    $isbn      = trim($_POST['isbn'] ?? '');
    $stok      = trim($_POST['stok'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');

    if (empty($id) || empty($judul) || empty($pengarang)) {
        $_SESSION['flash_message'] = "Data tidak valid!";
        $_SESSION['flash_type'] = "danger";
        header("Location: list.php");
        exit;
    }

    $sql = "UPDATE buku SET judul = :judul, pengarang = :pengarang, tahun = :tahun, isbn = :isbn, stok = :stok, kategori = :kategori WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    $update = $stmt->execute([
        ':id'        => $id,
        ':judul'     => $judul,
        ':pengarang' => $pengarang,
        ':tahun'     => (int)$tahun,
        ':isbn'      => $isbn,
        ':stok'      => (int)$stok,
        ':kategori'  => $kategori
    ]);

    if ($update) {
        $_SESSION['flash_message'] = "Data buku berhasil diperbarui!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Gagal memperbarui data buku.";
        $_SESSION['flash_type'] = "danger";
    }

    header("Location: list.php");
    exit;
}