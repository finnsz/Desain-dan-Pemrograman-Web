<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_anggota = trim($_POST['no_anggota'] ?? '');
    $nama       = trim($_POST['nama'] ?? '');
    $alamat     = trim($_POST['alamat'] ?? '');
    $no_hp      = trim($_POST['no_hp'] ?? '');

    // Validasi Server-Side
    if (empty($no_anggota) || empty($nama)) {
        $_SESSION['flash_message'] = "No. Anggota dan Nama wajib diisi!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // Simpan data ke $_SESSION
    $_SESSION['anggota'][] = [
        'no_anggota' => $no_anggota,
        'nama'       => $nama,
        'alamat'     => $alamat,
        'no_hp'      => $no_hp
    ];

    // Set Flash Message Sukses
    $_SESSION['flash_message'] = "Anggota berhasil ditambahkan!";
    $_SESSION['flash_type'] = "success";

    header("Location: list.php");
    exit;
}