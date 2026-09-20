<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul'] ?? '');
    $pengarang = trim($_POST['pengarang'] ?? '');
    $tahun     = trim($_POST['tahun'] ?? '');
    $isbn      = trim($_POST['isbn'] ?? '');
    $stok      = trim($_POST['stok'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');

    // Validasi Server-Side
    if (empty($judul) || empty($pengarang) || empty($tahun) || empty($stok)) {
        $_SESSION['flash_message'] = "Semua field yang wajib harus diisi!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // Simpan data ke $_SESSION
    $_SESSION['buku'][] = [
        'judul'     => $judul,
        'pengarang' => $pengarang,
        'tahun'     => (int)$tahun,
        'isbn'      => $isbn,
        'stok'      => (int)$stok,
        'kategori'  => $kategori
    ];

    // Set Flash Message Sukses
    $_SESSION['flash_message'] = "Buku berhasil ditambahkan!";
    $_SESSION['flash_type'] = "success";

    header("Location: list.php");
    exit;
}