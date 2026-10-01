<?php
require __DIR__ . '/../includes/auth.php';
$base = '../';
$title = 'Daftar Pengeluaran';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/role.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';
$filter_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$tipe_laporan = isset($_GET['tipe_laporan']) ? $_GET['tipe_laporan'] : 'seumur_hidup';
$filter_bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

$whereClause = "WHERE 1=1";
$params = [];

if ($search) {
    $whereClause .= " AND keterangan ILIKE :search";
    $params[':search'] = "%$search%";
}

if ($filter_kategori) {
    $whereClause .= " AND kategori = :kategori";
    $params[':kategori'] = $filter_kategori;
}

if ($tipe_laporan === 'bulanan') {
    $whereClause .= " AND EXTRACT(MONTH FROM tanggal) = :bulan AND EXTRACT(YEAR FROM tanggal) = :tahun";
    $params[':bulan'] = $filter_bulan;
    $params[':tahun'] = $filter_tahun;
} elseif ($tipe_laporan === 'tahunan') {
    $whereClause .= " AND EXTRACT(YEAR FROM tanggal) = :tahun";
    $params[':tahun'] = $filter_tahun;
}

// Hitung total
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM pengeluaran $whereClause");
$stmtTotal->execute($params);
$totalData = $stmtTotal->fetchColumn();
$totalPages = ceil($totalData / $limit);

// Ambil data dengan info user
$query = "SELECT p.*, u.nama as user_nama
          FROM pengeluaran p
          LEFT JOIN users u ON p.created_by = u.id
          $whereClause
          ORDER BY p.tanggal DESC, p.id DESC
          LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$pengeluaran_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil tahun unik
$tahun_list = $pdo->query("SELECT DISTINCT EXTRACT(YEAR FROM tanggal)::int as tahun FROM pengeluaran ORDER BY tahun DESC")->fetchAll(PDO::FETCH_COLUMN);

$bulan_nama = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<section class="card-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Daftar Pengeluaran</h2>
        <a href="tambah.php" class="btn-primary" style="font-size: 0.875rem; padding: 0.6rem 1.25rem;">
            <i class="fas fa-plus"></i> Tambah Pengeluaran
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari keterangan..." style="max-width: 200px;">

        <select name="kategori" style="max-width: 150px;">
            <option value="">Semua Kategori</option>
            <option value="Perbaikan" <?= $filter_kategori === 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
            <option value="Listrik" <?= $filter_kategori === 'Listrik' ? 'selected' : '' ?>>Listrik</option>
            <option value="Air" <?= $filter_kategori === 'Air' ? 'selected' : '' ?>>Air</option>
            <option value="Internet" <?= $filter_kategori === 'Internet' ? 'selected' : '' ?>>Internet</option>
            <option value="Gaji" <?= $filter_kategori === 'Gaji' ? 'selected' : '' ?>>Gaji</option>
            <option value="Lainnya" <?= $filter_kategori === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
        </select>

        <select name="tipe_laporan" id="tipeLaporan" style="max-width: 150px;" onchange="togglePeriod()">
            <option value="seumur_hidup" <?= $tipe_laporan === 'seumur_hidup' ? 'selected' : '' ?>>Seumur Hidup</option>
            <option value="tahunan" <?= $tipe_laporan === 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
            <option value="bulanan" <?= $tipe_laporan === 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
        </select>

        <select name="bulan" id="bulanSelect" style="max-width: 130px; <?= $tipe_laporan !== 'bulanan' ? 'display:none;' : '' ?>">
            <?php foreach ($bulan_nama as $num => $nama): ?>
                <option value="<?= $num ?>" <?= $filter_bulan === $num ? 'selected' : '' ?>><?= $nama ?></option>
            <?php endforeach; ?>
        </select>

        <select name="tahun" id="tahunSelect" style="max-width: 100px; <?= $tipe_laporan === 'seumur_hidup' ? 'display:none;' : '' ?>">
            <?php foreach ($tahun_list as $tahun): ?>
                <option value="<?= $tahun ?>" <?= $filter_tahun === $tahun ? 'selected' : '' ?>><?= $tahun ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-primary" style="padding: 0.6rem 1.25rem; font-size: 0.875rem;">Filter</button>
        <?php if ($search || $filter_kategori): ?>
            <a href="list.php" style="padding: 0.6rem 0.75rem; background: var(--danger-bg); color: var(--danger-text); border-radius: 8px; font-size: 0.875rem; font-weight: 500;">Reset</a>
        <?php endif; ?>
    </form>

    <script>
    function togglePeriod() {
        const tipe = document.getElementById('tipeLaporan').value;
        const bulan = document.getElementById('bulanSelect');
        const tahun = document.getElementById('tahunSelect');

        if (tipe === 'seumur_hidup') {
            bulan.style.display = 'none';
            tahun.style.display = 'none';
        } else if (tipe === 'tahunan') {
            bulan.style.display = 'none';
            tahun.style.display = 'block';
        } else {
            bulan.style.display = 'block';
            tahun.style.display = 'block';
        }
    }
    </script>

    <!-- Statistik Total -->
    <div style="background: #fee2e2; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center;">
        <?php
        $total_stmt = $pdo->prepare("SELECT COALESCE(SUM(nominal), 0) as total FROM pengeluaran $whereClause");
        $total_stmt->execute($params);
        $total_pengeluaran = $total_stmt->fetchColumn();
        ?>
        <div style="font-size: 1.5rem; font-weight: 700; color: var(--danger-text);">
            Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?>
        </div>
        <div style="font-size: 0.75rem; color: var(--danger-text); margin-top: 0.25rem;">
            Total Pengeluaran
            <?php
                if ($tipe_laporan === 'bulanan') {
                    echo '(' . $bulan_nama[$filter_bulan] . ' ' . $filter_tahun . ')';
                } elseif ($tipe_laporan === 'tahunan') {
                    echo '(' . $filter_tahun . ')';
                } else {
                    echo '(Seumur Hidup)';
                }
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th>Nominal</th>
                    <th>Dicatat Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pengeluaran_list)): ?>
                    <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">Belum ada data pengeluaran.</td></tr>
                <?php else: ?>
                    <?php foreach ($pengeluaran_list as $p): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($p['tanggal'])) ?></td>
                            <td><span class="badge badge-warning"><?= htmlspecialchars($p['kategori']) ?></span></td>
                            <td><?= htmlspecialchars($p['keterangan']) ?></td>
                            <td><strong style="color: var(--danger-text);">Rp <?= number_format($p['nominal'], 0, ',', '.') ?></strong></td>
                            <td style="font-size: 0.875rem; color: var(--text-secondary);">
                                <?= htmlspecialchars($p['user_nama'] ?? '-') ?>
                            </td>
                            <td>
                                <div class="aksi">
                                    <a href="edit.php?id=<?= $p['id'] ?>" class="btn-edit">Edit</a>
                                    <?php if (is_admin()): ?>
                                        <form action="hapus.php" method="POST" class="form-hapus" onsubmit="return confirm('Yakin hapus data ini?');">
                                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                            <button type="submit" class="btn-hapus">Hapus</button>
                                        </form>
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
            <a href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&kategori=<?= urlencode($filter_kategori) ?>&tipe_laporan=<?= urlencode($tipe_laporan) ?>&bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>"
               style="padding: 0.5rem 0.75rem; background: <?= $i === $page ? 'var(--primary)' : '#e5e7eb' ?>; color: <?= $i === $page ? 'white' : 'var(--text-primary)' ?>; border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
