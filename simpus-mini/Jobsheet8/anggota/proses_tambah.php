<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_anggota = trim($_POST['no_anggota'] ?? '');
    $nama       = trim($_POST['nama'] ?? '');
    $alamat     = trim($_POST['alamat'] ?? '');
    $no_hp      = trim($_POST['no_hp'] ?? '');

    if (empty($no_anggota) || empty($nama) || empty($alamat) || empty($no_hp)) {
        $_SESSION['flash_message'] = "Semua field wajib diisi!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // Cek apakah No. Anggota sudah terdaftar di database
    $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM anggota WHERE no_anggota = ?");
    $stmtCek->execute([$no_anggota]);
    if ($stmtCek->fetchColumn() > 0) {
        $_SESSION['flash_message'] = "No. Anggota '$no_anggota' sudah terdaftar!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // Insert ke PostgreSQL
    $sql = "INSERT INTO anggota (no_anggota, nama, alamat, no_hp) VALUES (:no_anggota, :nama, :alamat, :no_hp)";
    $stmt = $pdo->prepare($sql);
    $simpan = $stmt->execute([
        ':no_anggota' => $no_anggota,
        ':nama'       => $nama,
        ':alamat'     => $alamat,
        ':no_hp'      => $no_hp
    ]);

    if ($simpan) {
        $_SESSION['flash_message'] = "Anggota baru berhasil ditambahkan!";
        $_SESSION['flash_type'] = "success";
    }

    header("Location: list.php");
    exit;
}