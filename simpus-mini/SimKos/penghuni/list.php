<?php
require __DIR__ . '/../includes/auth.php';
// penghuni/list.php
$base = '../';
$title = 'Daftar Penghuni';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/role.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'p.id';
$dir = isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc' ? 'asc' : 'desc';

// Whitelist kolom yang boleh di-sort (cegah SQL injection)
$sortable = ['p.nama_lengkap', 'p.no_hp', 'k.nomor_kamar', 'k.tipe', 'k.harga', 'p.tgl_masuk', 'p.id'];
if (!in_array($sort, $sortable, true)) {
    $sort = 'p.id';
}
$orderBy = "$sort $dir";

$whereClause = "";
$params = [];

if ($search) {
    $whereClause = "WHERE p.nama_lengkap ILIKE :search OR p.no_hp ILIKE :search";
    $params[':search'] = "%$search%";
}

// Helper: buat URL dengan mempertahankan pencarian + sorting
function buildUrl($overrides = []) {
    $qs = array_merge([
        'q'    => $_GET['q'] ?? '',
        'sort' => $_GET['sort'] ?? 'p.id',
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
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM penghuni p $whereClause");
$stmtTotal->execute($params);
$totalData = $stmtTotal->fetchColumn();
$totalPages = ceil($totalData / $limit);

// Ambil data
$query = "SELECT p.*, k.nomor_kamar, k.tipe, k.harga
          FROM penghuni p
          LEFT JOIN kamar k ON p.kamar_id = k.id
          $whereClause
          ORDER BY $orderBy LIMIT :limit OFFSET :offset";
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
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <form method="GET" class="search-box">
                <input type="text" name="q" id="search-input" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama atau kamar...">
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
                    <th><?= sortLink('p.nama_lengkap', 'Nama Penghuni', $sort, $dir) ?></th>
                    <th><?= sortLink('p.no_hp', 'No. HP / WhatsApp', $sort, $dir) ?></th>
                    <th><?= sortLink('k.nomor_kamar', 'No. Kamar', $sort, $dir) ?></th>
                    <th><?= sortLink('k.tipe', 'Tipe Kamar', $sort, $dir) ?></th>
                    <th><?= sortLink('k.harga', 'Sewa / Bulan', $sort, $dir) ?></th>
                    <th><?= sortLink('p.tgl_masuk', 'Tanggal Masuk', $sort, $dir) ?></th>
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
                                <div class="aksi">
                                    <?php if ($sudahLogin): ?>
                                        <a href="edit.php?id=<?= $penghuni['id'] ?>" class="btn-edit">Edit</a>
                                    <?php endif; ?>
                                    <?php if (is_admin()): ?>
                                        <form action="hapus.php" method="POST" class="form-hapus" onsubmit="return confirm('Yakin hapus data ini?');">
                                            <input type="hidden" name="id" value="<?= $penghuni['id'] ?>">
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