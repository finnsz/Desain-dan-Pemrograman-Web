<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_anggota = trim($_POST['no_anggota'] ?? '');
    $nama       = trim($_POST['nama'] ?? '');
    $alamat     = trim($_POST['alamat'] ?? '');
    $no_hp      = trim($_POST['no_hp'] ?? '');

    // 1. Validasi field wajib
    if (empty($no_anggota) || empty($nama) || empty($alamat) || empty($no_hp)) {
        $_SESSION['flash_message'] = "Semua field wajib diisi!";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // 2. Validasi keunikan No. Anggota (Cek duplikasi di $_SESSION)
    if (isset($_SESSION['anggota'])) {
        foreach ($_SESSION['anggota'] as $agt) {
            if ($agt['no_anggota'] === $no_anggota) {
                $_SESSION['flash_message'] = "No. Anggota '$no_anggota' sudah terdaftar!";
                $_SESSION['flash_type'] = "danger";
                header("Location: tambah.php");
                exit;
            }
        }
    }

    // 3. Validasi No. HP (Harus angka dan panjang 10-13 digit)
    if (!preg_match('/^[0-9]{10,13}$/', $no_hp)) {
        $_SESSION['flash_message'] = "No. HP tidak valid! Harus berupa angka 10-13 digit.";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // 4. Validasi minimal panjang alamat
    if (strlen($alamat) < 5) {
        $_SESSION['flash_message'] = "Alamat terlalu pendek! Minimal 5 karakter.";
        $_SESSION['flash_type'] = "danger";
        header("Location: tambah.php");
        exit;
    }

    // Simpan ke SESSION jika semua validasi lulus
    $_SESSION['anggota'][] = [
        'no_anggota' => $no_anggota,
        'nama'       => $nama,
        'alamat'     => $alamat,
        'no_hp'      => $no_hp
    ];

    $_SESSION['flash_message'] = "Anggota baru berhasil ditambahkan!";
    $_SESSION['flash_type'] = "success";

    header("Location: list.php");
    exit;
}