<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';

$totalBuku = count($_SESSION['buku'] ?? []);
$totalAnggota = count($_SESSION['anggota'] ?? []);
?>
        <section class="hero">
            <div class="hero-text">
                <h2>Selamat Datang di Sistem Perpustakaan Mini</h2>
                <p>Aplikasi sederhana untuk mengelola data buku dan anggota perpustakaan.</p>
            </div>
            <div class="hero-actions">
                <span class="hero-badge">Total Data: <?php echo $totalBuku + $totalAnggota; ?></span>
                <a href="<?php echo $base; ?>buku/list.php" class="hero-btn">Lihat Buku</a>
                <a href="<?php echo $base; ?>anggota/list.php" class="hero-btn">Lihat Anggota</a>
            </div>
        </section>

        <section>
            <h2>Ringkasan</h2>
            <article>
                <h3>Total Buku</h3>
                <p><?php echo $totalBuku; ?></p>
            </article>
            <article>
                <h3>Total Anggota</h3>
                <p><?php echo $totalAnggota; ?></p>
            </article>
            <article>
                <h3>Sedang Dipinjam</h3>
                <p>0</p>
            </article>
        </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
