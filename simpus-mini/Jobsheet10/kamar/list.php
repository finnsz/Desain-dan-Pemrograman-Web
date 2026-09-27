<?php
// kamar/list.php
$base = '../';
$title = 'Daftar Kamar';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';

$whereClause = "";
$params = [];
if ($search) {
    $whereClause = "WHERE nomor_kamar ILIKE :search OR tipe ILIKE :search";
    $params[':search'] = "%$search%";
}

// Hitung total
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM kamar $whereClause");
$stmtTotal->execute($params);
$totalData = $stmtTotal->fetchColumn();
$totalPages = ceil($totalData / $limit);

// Ambil data
$query = "SELECT * FROM kamar $whereClause ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$kamar_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="card-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Daftar Kamar Kost</h2>
        <form method="GET" class="search-box">
            <input type="text" name="q" id="search-input" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nomor / tipe kamar...">
        </form>
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
                                <a href="edit.php?id=<?= $kamar['id'] ?>">Edit</a>
                                <form action="hapus.php" method="POST" class="form-hapus" style="display:inline;" onsubmit="return confirm('Yakin hapus?');">
                                    <input type="hidden" name="id" value="<?= $kamar['id'] ?>">
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