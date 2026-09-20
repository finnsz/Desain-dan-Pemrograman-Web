<?php
$base = '../';
$title = 'SIMPUS-Mini | Daftar Anggota';

// Panggil file koneksi PDO menggunakan __DIR__
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

// Ambil data anggota dari PostgreSQL
$stmt = $pdo->query("SELECT * FROM anggota ORDER BY id DESC");
$anggota_list = $stmt->fetchAll();
?>

<section>
    <h2>Daftar Anggota</h2>

    <div class="search-box">
        <input type="text" id="search-input" placeholder="Cari nama atau no. anggota...">
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Anggota</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. HP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($anggota_list)): ?>
                    <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">Belum ada data anggota di database.</td></tr>
                <?php else: ?>
                    <?php foreach ($anggota_list as $anggota): ?>
                        <tr>
                            <td><code style="color: var(--accent-cyan);"><?= htmlspecialchars($anggota['no_anggota']) ?></code></td>
                            <td><strong><?= htmlspecialchars($anggota['nama']) ?></strong></td>
                            <td><?= htmlspecialchars($anggota['alamat'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($anggota['no_hp'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>