<?php
$base = '../';
$title = 'SIMPUS-Mini | Daftar Buku';

// Panggil file koneksi PDO
require_once '../config/database.php';
include '../includes/header.php';

// Ambil data dari tabel buku PostgreSQL
$stmt = $pdo->query("SELECT * FROM buku ORDER BY id DESC");
$buku_list = $stmt->fetchAll();
?>

<section>
    <h2>Daftar Buku</h2>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun</th>
                    <th>ISBN</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($buku_list)): ?>
                    <tr><td colspan="7">Belum ada data buku di database.</td></tr>
                <?php else: ?>
                    <?php foreach ($buku_list as $buku): ?>
                        <tr>
                            <td><?= htmlspecialchars($buku['judul']) ?></td>
                            <td><?= htmlspecialchars($buku['pengarang']) ?></td>
                            <td><?= htmlspecialchars($buku['tahun']) ?></td>
                            <td><?= htmlspecialchars($buku['isbn'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($buku['stok']) ?></td>
                            <td><?= htmlspecialchars($buku['kategori']) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $buku['id'] ?>">Edit</a> | 
                                <a href="hapus.php?id=<?= $buku['id'] ?>" onclick="return confirm('Yakin hapus buku ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include '../includes/footer.php'; ?>