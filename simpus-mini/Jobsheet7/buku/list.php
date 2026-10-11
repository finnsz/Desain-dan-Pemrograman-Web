<?php
$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$daftarBuku = $_SESSION['buku'] ?? [];
?>
        <section class="card-list-section">
            <h2>Daftar Buku</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <label for="search-input">Cari Judul Buku</label>
                <input type="text" id="search-input" placeholder="Ketik judul buku...">
            </div>

            <?php if (empty($daftarBuku)): ?>
                <p class="empty-state">Belum ada data buku. Silakan tambah lewat menu "Tambah Buku".</p>
            <?php else: ?>
            <div class="card-grid">
                <?php foreach ($daftarBuku as $buku): ?>
                <article class="card-item">
                    <h3><?php echo htmlspecialchars($buku['judul']); ?></h3>
                    <p class="card-meta"><?php echo htmlspecialchars($buku['pengarang']); ?> &middot; <?php echo htmlspecialchars($buku['tahun']); ?></p>
                    <p class="card-stok">Stok: <strong><?php echo (int)$buku['stok']; ?></strong></p>
                    <div class="card-actions">
                        <a href="<?php echo $base; ?>buku/tambah.php" class="btn-edit">Edit</a>
                        <button type="button" class="btn-hapus" onclick="return confirm('Yakin hapus buku ini?')">Hapus</button>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
