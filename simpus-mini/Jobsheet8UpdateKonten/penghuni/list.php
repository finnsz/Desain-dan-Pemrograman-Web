<?php
$base = '../';
$title = 'Daftar Penghuni';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

// Perbaikan: Ubah p.nama menjadi p.nama_lengkap & p.kamar_id
$query = "SELECT p.id, p.nama_lengkap, p.no_hp, p.tgl_masuk, k.nomor_kamar, k.tipe, k.harga 
          FROM penghuni p 
          LEFT JOIN kamar k ON p.kamar_id = k.id 
          ORDER BY p.id DESC";

$stmt = $pdo->query($query);
$penghuni_list = $stmt->fetchAll();
?>

<section class="card-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Daftar Penghuni Kost</h2>
        <div class="search-box">
            <input type="text" id="search-input" placeholder="Cari nama atau kamar...">
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Penghuni</th>
                    <th>No. HP / WhatsApp</th>
                    <th>No. Kamar</th>
                    <th>Tipe Kamar</th>
                    <th>Sewa / Bulan</th>
                    <th>Tanggal Masuk</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($penghuni_list)): ?>
                    <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">Belum ada data penghuni.</td></tr>
                <?php else: ?>
                    <?php foreach ($penghuni_list as $penghuni): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($penghuni['nama_lengkap']) ?></strong></td>
                            <td><?= htmlspecialchars($penghuni['no_hp']) ?></td>
                            <td>
                                <?php if (!empty($penghuni['nomor_kamar'])): ?>
                                    <span class="badge badge-warning"><?= htmlspecialchars($penghuni['nomor_kamar']) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($penghuni['tipe'] ?? '-') ?></td>
                            <td>Rp <?= number_format($penghuni['harga'] ?? 0, 0, ',', '.') ?></td>
                            <td><?= !empty($penghuni['tgl_masuk']) ? date('d M Y', strtotime($penghuni['tgl_masuk'])) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>