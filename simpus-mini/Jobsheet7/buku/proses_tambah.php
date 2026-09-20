<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul'] ?? '');
    $pengarang = trim($_POST['pengarang'] ?? '');
    $tahun     = trim($_POST['tahun'] ?? '');
    $isbn      = trim($_POST['isbn'] ?? '');
    $stok      = trim($_POST['stok'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');

    // 1. Validasi field wajib
    if (empty($judul) || empty($pengarang) || empty($tahun) || $stok === '') {
        $_SESSION['flash_message'] = "Semua field yang wajib diisi tidak boleh kosong!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // 2. Validasi tahun (rentang wajar, misal 1000 s.d. tahun sekarang)
    $tahun_sekarang = (int)date('Y');
    if (!is_numeric($tahun) || (int)$tahun < 1000 || (int)$tahun > $tahun_sekarang) {
        $_SESSION['flash_message'] = "Tahun terbit tidak valid (harus antara 1000 - $tahun_sekarang)!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // 3. Validasi stok (tidak boleh negatif)
    if (!is_numeric($stok) || (int)$stok < 0) {
        $_SESSION['flash_message'] = "Stok tidak boleh bernilai negatif!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // 4. Validasi ISBN (jika diisi, hanya boleh angka dan tanda hubung '-')
    if (!empty($isbn) && !preg_match('/^[0-9-]+$/', $isbn)) {
        $_SESSION['flash_message'] = "Format ISBN tidak valid! Hanya boleh berisi angka dan tanda hubung (-).";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // Simpan ke SESSION jika lulus semua validasi
    $_SESSION['buku'][] = [
        'judul'     => $judul,
        'pengarang' => $pengarang,
        'tahun'     => (int)$tahun,
        'isbn'      => $isbn,
        'stok'      => (int)$stok,
        'kategori'  => $kategori
    ];

    $_SESSION['flash_message'] = "Buku berhasil ditambahkan!";
    $_SESSION['flash_type'] = "success";

    header("Location: list.php");
    exit;
}