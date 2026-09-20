<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Default path base jika tidak didefinisikan di halaman utama
$base = $base ?? './';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'SIMPUS-Mini' ?></title>
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
    <header>
        <h1>SIMPUS-Mini</h1>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li><a href="<?= $base ?>index.php">Beranda</a></li>
                <li><a href="<?= $base ?>buku/list.php">Daftar Buku</a></li>
                <li><a href="<?= $base ?>buku/tambah.php">Tambah Buku</a></li>
                <li><a href="<?= $base ?>anggota/list.php">Daftar Anggota</a></li>
                <li><a href="<?= $base ?>anggota/tambah.php">Tambah Anggota</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?>" style="padding: 10px; margin-bottom: 15px; background: #2d88cd; color: #fff; border-radius: 4px;">
                <?= $_SESSION['flash_message']; ?>
            </div>
            <?php 
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']);
            ?>
        <?php endif; ?>