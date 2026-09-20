<?php
session_start();
require_once '../config/database.php'; // Panggil PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul'] ?? '');
    $pengarang = trim($_POST['pengarang'] ?? '');
    $tahun     = trim($_POST['tahun'] ?? '');
    $isbn      = trim($_POST['isbn'] ?? '');
    $stok      = trim($_POST['stok'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');

    if (empty($judul) || empty($pengarang) || empty($tahun) || $stok === '') {
        $_SESSION['flash_message'] = "Field wajib harus diisi!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // Gunakan parameter bawaan PDO (:nama_kolom) untuk keamanan dari SQL Injection
    $sql = "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori) 
            VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori)";
    
    $stmt = $pdo->prepare($sql);
    $simpan = $stmt->execute([
        ':judul'     => $judul,
        ':pengarang' => $pengarang,
        ':tahun'     => (int)$tahun,
        ':isbn'      => $isbn,
        ':stok'      => (int)$stok,
        ':kategori'  => $kategori
    ]);

    if ($simpan) {
        $_SESSION['flash_message'] = "Buku berhasil ditambahkan ke Database!";
        $_SESSION['flash_type'] = "success";
    }

    header("Location: list.php");
    exit;
}