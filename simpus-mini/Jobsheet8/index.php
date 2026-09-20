<?php
$base = './';
$title = 'SIMPUS-Mini | Beranda';

require_once 'config/database.php';
include 'includes/header.php';

// Hitung total data langsung dari PostgreSQL
$total_buku = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$total_anggota = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
?>

<section>
    <h2>Selamat Datang di Sistem Perpustakaan Mini</h2>
    <p>Aplikasi sederhana untuk mengelola data buku dan anggota perpustakaan.</p>
</section>

<section>
    <h2>Ringkasan</h2>
    <article>
        <h3>Total Buku</h3>
        <p><?= $total_buku ?></p>
    </article>
    <article>
        <h3>Total Anggota</h3>
        <p><?= $total_anggota ?></p>
    </article>
</section>

<?php include 'includes/footer.php'; ?>