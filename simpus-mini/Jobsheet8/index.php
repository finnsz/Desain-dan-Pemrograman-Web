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
    <h2>Selamat Datang di SIMPUS-Mini</h2>
    <p style="color: var(--text-muted);">Sistem informasi manajemen perpustakaan sederhana berbasis Web & PostgreSQL.</p>
</section>

<section>
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