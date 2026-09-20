<?php
$base = '../';
$title = 'SIMPUS-Mini | Daftar Buku';
include '../includes/header.php';

$buku_list = $_SESSION['buku'] ?? [];
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
                </tr>
            </thead>
            <tbody>
                <?php if (empty($buku_list)): ?>
                    <tr><td colspan="6">Belum ada data buku.</td></tr>
                <?php else: ?>
                    <?php foreach ($buku_list as $buku): ?>
                        <tr>
                            <td><?= htmlspecialchars($buku['judul']) ?></td>
                            <td><?= htmlspecialchars($buku['pengarang']) ?></td>
                            <td><?= htmlspecialchars($buku['tahun']) ?></td>
                            <td><?= htmlspecialchars($buku['isbn'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($buku['stok']) ?></td>
                            <td><?= htmlspecialchars($buku['kategori']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include '../includes/footer.php'; ?>