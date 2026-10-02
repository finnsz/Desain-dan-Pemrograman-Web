<?php
// kamar/list.php
$base = '../';
$title = 'Daftar Kamar';

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/role.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$dir = isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc' ? 'asc' : 'desc';

// Whitelist kolom yang boleh di-sort (cegah SQL injection)
$sortable = ['nomor_kamar', 'tipe', 'harga', 'status', 'id'];
if (!in_array($sort, $sortable, true)) {
    $sort = 'id';
}
$orderBy = "$sort $dir";

$whereClause = "";
$params = [];

if ($search) {
    $whereClause = "WHERE nomor_kamar ILIKE :search OR tipe ILIKE :search";
    $params[':search'] = "%$search%";
}

// Helper: buat URL dengan mempertahankan pencarian + sorting
function buildUrl($overrides = []) {
    $qs = array_merge([
        'q'    => $_GET['q'] ?? '',
        'sort' => $_GET['sort'] ?? 'id',
        'dir'  => $_GET['dir'] ?? 'desc',
        'page' => $_GET['page'] ?? 1,
    ], $overrides);
    return '?' . http_build_query(array_filter($qs, fn($v) => $v !== '' && $v !== null));
}

// Helper: link header sorting (klik = toggle asc/desc)
function sortLink($column, $label, $currentSort, $currentDir) {
    $isActive = $currentSort === $column;
    $nextDir = $isActive && $currentDir === 'asc' ? 'desc' : 'asc';
    $arrow = $isActive ? ($currentDir === 'asc' ? ' ▲' : ' ▼') : ' ⇅';
    $url = buildUrl(['sort' => $column, 'dir' => $nextDir, 'page' => 1]);
    $style = $isActive ? 'color: var(--primary);' : 'color: var(--text-secondary);';
    return '<a href="' . $url . '" style="' . $style . ' display: inline-flex; align-items: center; gap: 0.3rem; white-space: nowrap; cursor: pointer;">'
         . htmlspecialchars($label) . '<span style="font-size: 0.8em; opacity: 0.7;">' . $arrow . '</span></a>';
}

// Hitung total
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM kamar $whereClause");
$stmtTotal->execute($params);
$totalData = $stmtTotal->fetchColumn();
$totalPages = ceil($totalData / $limit);

// Ambil data
$query = "SELECT * FROM kamar $whereClause ORDER BY $orderBy LIMIT :limit OFFSET :offset";
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
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <form method="GET" class="search-box">
                <input type="text" name="q" id="search-input" value="<?php echo e($search); ?>" placeholder="Cari nomor / tipe kamar...">
            </form>
            <?php if ($search): ?>
                <a href="list.php" style="padding: 0.5rem 0.75rem; background: var(--danger-bg); color: var(--danger-text); border-radius: 8px; font-size: 0.875rem; font-weight: 500;">Reset Filter</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= sortLink('nomor_kamar', 'No. Kamar', $sort, $dir) ?></th>
                    <th><?= sortLink('tipe', 'Tipe', $sort, $dir) ?></th>
                    <th><?= sortLink('harga', 'Harga / Bulan', $sort, $dir) ?></th>
                    <th><?= sortLink('status', 'Status', $sort, $dir) ?></th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kamar_list)): ?>
                    <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada data kamar.</td></tr>
                <?php else: ?>
                    <?php foreach ($kamar_list as $kamar): ?>
                        <tr>
                            <td><strong><?php echo e($kamar['nomor_kamar']); ?></strong></td>
                            <td><?php echo e($kamar['tipe']); ?></td>
                            <td>Rp <?= number_format($kamar['harga'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge <?= $kamar['status'] === 'TERISI' ? 'badge-warning' : 'badge-success' ?>">
                                    <?php echo e($kamar['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="aksi">
                                    <?php if ($sudahLogin): ?>
                                        <a href="edit.php?id=<?= $kamar['id'] ?>" class="btn-edit">Edit</a>
                                    <?php endif; ?>
                                    <?php if (is_admin()): ?>
                                        <form action="hapus.php" method="POST" class="form-hapus" onsubmit="return confirm('Yakin hapus data ini?');">
                                            <input type="hidden" name="id" value="<?= $kamar['id'] ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn-hapus">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (!$sudahLogin): ?>
                                        <span style="color: var(--text-muted);">-</span>
                                    <?php endif; ?>
                                </div>
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
            <a href="<?= buildUrl(['page' => $i]) ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>