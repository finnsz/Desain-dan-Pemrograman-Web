<?php
// penghuni/list.php
$base = '../';
$title = 'Daftar Penghuni';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';

$whereClause = "";
$params = [];
if ($search) {
    $whereClause = "WHERE p.nama_lengkap ILIKE :search OR p.no_hp ILIKE :search";
    $params[':search'] = "%$search%";
}

// Hitung total
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM penghuni p $whereClause");
$stmtTotal->execute($params);
$totalData = $stmtTotal->fetchColumn();
$totalPages = ceil($totalData / $limit);

// Ambil data
$query = "SELECT p.*, k.nomor_kamar, k.tipe, k.harga
          FROM penghuni p
          LEFT JOIN kamar k ON p.kamar_id = k.id
          $whereClause
          ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$penghuni_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="card-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Daftar Penghuni Kost</h2>
        <form method="GET" class="search-box">
            <input type="text" name="q" id="search-input" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama atau kamar...">
        </form>
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($penghuni_list)): ?>
                    <tr><td colspan="7" style="text-align: center; color: var(--text-muted);">Belum ada data penghuni.</td></tr>
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
                            <td>
                                <a href="edit.php?id=<?= $penghuni['id'] ?>">Edit</a>
                                <form action="hapus.php" method="POST" class="form-hapus" style="display:inline;" onsubmit="return confirm('Yakin hapus?');">
                                    <input type="hidden" name="id" value="<?= $penghuni['id'] ?>">
                                    <button type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div style="margin-top: 1.25rem; display: flex; gap: 0.5rem;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&q=<?= urlencode($search) ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>