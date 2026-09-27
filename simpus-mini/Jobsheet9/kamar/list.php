<?php
$base = '../';
$title = 'Daftar Kamar';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("SELECT * FROM kamar ORDER BY nomor_kamar ASC");
$kamar_list = $stmt->fetchAll();
?>
<section class="card-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Daftar Kamar Kost</h2>
        <div class="search-box">
            <input type="text" id="search-input" placeholder="Cari nomor / tipe kamar...">
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Kamar</th>
                    <th>Tipe</th>
                    <th>Harga / Bulan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kamar_list)): ?>
                    <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada data kamar.</td></tr>
                <?php else: ?>
                    <?php foreach ($kamar_list as $kamar): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($kamar['nomor_kamar']) ?></strong></td>
                            <td><?= htmlspecialchars($kamar['tipe']) ?></td>
                            <td>Rp <?= number_format($kamar['harga'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge <?= $kamar['status'] === 'TERISI' ? 'badge-warning' : 'badge-success' ?>">
                                    <?= htmlspecialchars($kamar['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $kamar['id'] ?>" class="btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">Edit</a>
                                <a href="hapus.php?id=<?= $kamar['id'] ?>" class="btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; background-color: var(--danger-bg); color: var(--danger-text);" onclick="return confirm('Yakin ingin menghapus kamar ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>