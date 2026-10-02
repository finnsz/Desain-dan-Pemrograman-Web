<?php
require __DIR__ . '/../includes/auth.php';
$base = '../';
$title = 'Daftar Pembayaran';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/role.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : 0;
$filter_tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : 0;

// Sorting
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'periode_tahun';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Validasi sort_by untuk keamanan
$allowed_sorts = ['nama_lengkap', 'nomor_kamar', 'periode_bulan', 'nominal', 'denda', 'total_bayar', 'tgl_jatuh_tempo', 'tgl_bayar', 'status'];
if (!in_array($sort_by, $allowed_sorts)) {
    $sort_by = 'periode_tahun';
}

// Build ORDER BY dengan secondary sort untuk consistency
$order_by = "$sort_by $sort_order";
// Tambahkan secondary sort berdasarkan primary sort column
if ($sort_by === 'periode_bulan') {
    $order_by .= ", periode_tahun $sort_order, tgl_jatuh_tempo $sort_order";
} elseif ($sort_by === 'status') {
    $order_by .= ", tgl_jatuh_tempo DESC";
} elseif ($sort_by === 'nama_lengkap') {
    $order_by .= ", periode_tahun DESC, periode_bulan DESC";
} elseif ($sort_by === 'nomor_kamar') {
    $order_by .= ", periode_tahun DESC, periode_bulan DESC";
} else {
    // Default secondary: periode desc
    $order_by .= ", periode_tahun DESC, periode_bulan DESC";
}

// Toggle sort order
$next_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

$whereClause = "WHERE 1=1";
$params = [];

if ($search) {
    $whereClause .= " AND (nama_lengkap ILIKE :search OR nomor_kamar ILIKE :search)";
    $params[':search'] = "%$search%";
}

if ($filter_status) {
    $whereClause .= " AND status = :status";
    $params[':status'] = $filter_status;
}

if ($filter_bulan > 0) {
    $whereClause .= " AND periode_bulan = :bulan";
    $params[':bulan'] = $filter_bulan;
}

if ($filter_tahun > 0) {
    $whereClause .= " AND periode_tahun = :tahun";
    $params[':tahun'] = $filter_tahun;
}

// Hitung total
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM v_pembayaran_detail $whereClause");
$stmtTotal->execute($params);
$totalData = $stmtTotal->fetchColumn();
$totalPages = ceil($totalData / $limit);

// Ambil data dengan explicit JOIN pattern (tidak pakai view)
// JOIN menggabungkan 3 tabel: pembayaran (pb), penghuni (p), kamar (k)
// - pb.penghuni_id dirujuk ke p.id (many-to-one)
// - p.kamar_id dirujuk ke k.id (many-to-one)
$query = "SELECT pb.id, pb.penghuni_id, p.nama_lengkap, p.no_hp,
                 k.nomor_kamar, k.tipe, k.harga as harga_sewa,
                 pb.periode_bulan, pb.periode_tahun, pb.nominal, pb.denda, pb.total_bayar,
                 pb.tgl_jatuh_tempo, pb.tgl_bayar, pb.status, pb.metode_bayar,
                 CASE
                   WHEN pb.tgl_bayar IS NULL AND CURRENT_DATE > pb.tgl_jatuh_tempo THEN true
                   ELSE false
                 END as is_terlambat,
                 CASE
                   WHEN pb.tgl_bayar IS NULL AND CURRENT_DATE > pb.tgl_jatuh_tempo
                   THEN CURRENT_DATE - pb.tgl_jatuh_tempo
                   ELSE 0
                 END as hari_terlambat
          FROM pembayaran pb
          JOIN penghuni p ON pb.penghuni_id = p.id
          LEFT JOIN kamar k ON p.kamar_id = k.id
          $whereClause
          ORDER BY $order_by
          LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$pembayaran_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil tahun unik untuk filter
$tahun_list = $pdo->query("SELECT DISTINCT periode_tahun FROM pembayaran ORDER BY periode_tahun DESC")->fetchAll(PDO::FETCH_COLUMN);

$bulan_nama = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<section class="card-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Daftar Pembayaran Sewa</h2>
        <div style="display: flex; gap: 0.75rem;">
            <a href="generate.php" class="btn-primary" style="font-size: 0.875rem; padding: 0.6rem 1.25rem;">
                <i class="fas fa-magic"></i> Generate Tagihan
            </a>
            <a href="tambah.php" class="btn-secondary" style="font-size: 0.875rem; padding: 0.6rem 1.25rem;">
                <i class="fas fa-plus"></i> Catat Pembayaran
            </a>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / kamar..." style="max-width: 250px; padding: 0.75rem;">

        <select name="status" style="max-width: 180px; padding: 0.75rem;">
            <option value="">Semua Status</option>
            <option value="BELUM BAYAR" <?= $filter_status === 'BELUM BAYAR' ? 'selected' : '' ?>>Belum Bayar</option>
            <option value="LUNAS" <?= $filter_status === 'LUNAS' ? 'selected' : '' ?>>Lunas</option>
            <option value="TERLAMBAT" <?= $filter_status === 'TERLAMBAT' ? 'selected' : '' ?>>Terlambat</option>
        </select>

        <select name="bulan" style="max-width: 180px; padding: 0.75rem;">
            <option value="0">Semua Bulan</option>
            <?php foreach ($bulan_nama as $num => $nama): ?>
                <option value="<?= $num ?>" <?= $filter_bulan === $num ? 'selected' : '' ?>><?= $nama ?></option>
            <?php endforeach; ?>
        </select>

        <select name="tahun" style="max-width: 150px; padding: 0.75rem;">
            <option value="0">Semua Tahun</option>
            <?php foreach ($tahun_list as $tahun): ?>
                <option value="<?= $tahun ?>" <?= $filter_tahun === $tahun ? 'selected' : '' ?>><?= $tahun ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-primary" style="padding: 0.75rem 1.5rem; font-size: 1rem;">Filter</button>
        <?php if ($search || $filter_status || $filter_bulan || $filter_tahun): ?>
            <a href="list.php" style="padding: 0.75rem 1rem; background: var(--danger-bg); color: var(--danger-text); border-radius: 8px; font-size: 1rem; font-weight: 500;">Reset</a>
        <?php endif; ?>
    </form>

    <!-- Statistik Cepat -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <?php
        $stats = $pdo->prepare("SELECT
            COUNT(*) FILTER (WHERE status = 'BELUM BAYAR') as belum_bayar,
            COUNT(*) FILTER (WHERE status = 'LUNAS') as lunas,
            COUNT(*) FILTER (WHERE is_terlambat = true AND status != 'LUNAS') as terlambat,
            COALESCE(SUM(total_bayar) FILTER (WHERE status = 'LUNAS'), 0) as total_terbayar
        FROM v_pembayaran_detail $whereClause");
        $stats->execute($params);
        $stat = $stats->fetch();
        ?>
        <div style="background: var(--success-bg); padding: 1rem; border-radius: 10px; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 700; color: var(--success-text);"><?= $stat['lunas'] ?></div>
            <div style="font-size: 0.75rem; color: var(--success-text); margin-top: 0.25rem;">Lunas</div>
        </div>
        <div style="background: var(--warning-bg); padding: 1rem; border-radius: 10px; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 700; color: var(--warning-text);"><?= $stat['belum_bayar'] ?></div>
            <div style="font-size: 0.75rem; color: var(--warning-text); margin-top: 0.25rem;">Belum Bayar</div>
        </div>
        <div style="background: var(--danger-bg); padding: 1rem; border-radius: 10px; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 700; color: var(--danger-text);"><?= $stat['terlambat'] ?></div>
            <div style="font-size: 0.75rem; color: var(--danger-text); margin-top: 0.25rem;">Terlambat</div>
        </div>
        <div style="background: #dbeafe; padding: 1rem; border-radius: 10px; text-align: center;">
            <div style="font-size: 1.1rem; font-weight: 700; color: #3b82f6;">Rp <?= number_format($stat['total_terbayar'], 0, ',', '.') ?></div>
            <div style="font-size: 0.75rem; color: #3b82f6; margin-top: 0.25rem;">Total Terbayar</div>
        </div>
    </div>

    <div class="table-responsive">
        <table style="font-size: 0.95rem;">
            <thead>
                <tr>
                    <?php
                    function sortLink($column, $label, $current_sort, $current_order) {
                        global $search, $filter_status, $filter_bulan, $filter_tahun;
                        $next_order = ($current_sort === $column && $current_order === 'ASC') ? 'DESC' : 'ASC';
                        $icon = '';
                        if ($current_sort === $column) {
                            $icon = $current_order === 'ASC' ? ' <i class="fas fa-arrow-up" style="margin-left: 0.25rem;"></i>' : ' <i class="fas fa-arrow-down" style="margin-left: 0.25rem;"></i>';
                        } else {
                            $icon = ' <i class="fas fa-arrows-alt-v" style="margin-left: 0.25rem; opacity: 0.5;"></i>';
                        }
                        return '<a href="?sort=' . $column . '&order=' . $next_order .
                               '&q=' . urlencode($search) .
                               '&status=' . urlencode($filter_status) .
                               '&bulan=' . $filter_bulan .
                               '&tahun=' . $filter_tahun .
                               '" style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">' . $label . $icon . '</a>';
                    }
                    ?>
                    <th style="min-width: 100px; padding: 1rem 0.75rem;"><?= sortLink('nama_lengkap', 'Penghuni', $sort_by, $sort_order) ?></th>
                    <th style="min-width: 60px; padding: 1rem 0.75rem;"><?= sortLink('nomor_kamar', 'Kamar', $sort_by, $sort_order) ?></th>
                    <th style="min-width: 85px; padding: 1rem 0.75rem;"><?= sortLink('periode_bulan', 'Periode', $sort_by, $sort_order) ?></th>
                    <th style="min-width: 80px; padding: 1rem 0.75rem;"><?= sortLink('nominal', 'Nominal', $sort_by, $sort_order) ?></th>
                    <th style="min-width: 70px; padding: 1rem 0.75rem;"><?= sortLink('denda', 'Denda', $sort_by, $sort_order) ?></th>
                    <th style="min-width: 80px; padding: 1rem 0.75rem;"><?= sortLink('total_bayar', 'Total', $sort_by, $sort_order) ?></th>
                    <th style="min-width: 70px; padding: 1rem 0.75rem;"><?= sortLink('tgl_jatuh_tempo', 'Tempo', $sort_by, $sort_order) ?></th>
                    <th style="min-width: 70px; padding: 1rem 0.75rem;"><?= sortLink('tgl_bayar', 'Bayar', $sort_by, $sort_order) ?></th>
                    <th style="min-width: 70px; padding: 1rem 0.75rem;"><?= sortLink('status', 'Status', $sort_by, $sort_order) ?></th>
                    <th style="min-width: 60px; padding: 1rem 0.75rem;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pembayaran_list)): ?>
                    <tr><td colspan="10" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">Belum ada data pembayaran.</td></tr>
                <?php else: ?>
                    <?php foreach ($pembayaran_list as $p): ?>
                        <tr>
                            <td style="white-space: nowrap; padding: 0.85rem 0.75rem;"><strong><?= htmlspecialchars($p['nama_lengkap']) ?></strong></td>
                            <td style="padding: 0.85rem 0.75rem;"><span class="badge badge-warning" style="padding: 0.4rem 0.65rem;"><?= htmlspecialchars($p['nomor_kamar']) ?></span></td>
                            <td style="white-space: nowrap; padding: 0.85rem 0.75rem;"><?= substr($bulan_nama[$p['periode_bulan']], 0, 3) ?> <?= $p['periode_tahun'] ?></td>
                            <td style="white-space: nowrap; padding: 0.85rem 0.75rem;"><?= number_format($p['nominal'] / 1000, 0) ?>k</td>
                            <td style="white-space: nowrap; padding: 0.85rem 0.75rem;">
                                <?php
                                // Hitung denda otomatis jika terlambat (Rp 50.000/hari, max 500k)
                                $denda_display = $p['denda'];
                                if ($p['is_terlambat'] && $p['status'] !== 'LUNAS' && $p['hari_terlambat'] > 0) {
                                    $denda_display = $p['hari_terlambat'] * 50000;
                                    if ($denda_display > 500000) $denda_display = 500000;
                                }
                                echo $denda_display > 0 ? number_format($denda_display / 1000, 0) . 'k' : '-';
                                ?>
                            </td>
                            <td style="white-space: nowrap; padding: 0.85rem 0.75rem;"><strong><?= number_format(($p['total_bayar'] + ($denda_display - $p['denda'])) / 1000, 0) ?>k</strong></td>
                            <td style="white-space: nowrap; padding: 0.85rem 0.75rem;"><?= date('d/m/y', strtotime($p['tgl_jatuh_tempo'])) ?></td>
                            <td style="white-space: nowrap; padding: 0.85rem 0.75rem;"><?= $p['tgl_bayar'] ? date('d/m/y', strtotime($p['tgl_bayar'])) : '-' ?></td>
                            <td style="padding: 0.85rem 0.75rem;">
                                <?php
                                $badge_class = 'badge-success';
                                $status = $p['status'];
                                $status_text = 'Lunas';
                                if ($p['is_terlambat'] && $status !== 'LUNAS') {
                                    $badge_class = 'badge-danger';
                                    $status_text = 'Telat';
                                } elseif ($status === 'BELUM BAYAR') {
                                    $badge_class = 'badge-warning';
                                    $status_text = 'Belum';
                                }
                                ?>
                                <span class="badge <?= $badge_class ?>" style="padding: 0.4rem 0.65rem;"><?= $status_text ?></span>
                            </td>
                            <td style="padding: 0.85rem 0.75rem;">
                                <a href="edit.php?id=<?= $p['id'] ?>" class="btn-edit" style="padding: 0.5rem 0.75rem; font-size: 0.9rem;">Edit</a>
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
            <a href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&status=<?= urlencode($filter_status) ?>&bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>&sort=<?= urlencode($sort_by) ?>&order=<?= urlencode($sort_order) ?>"
               style="padding: 0.5rem 0.75rem; background: <?= $i === $page ? 'var(--primary)' : '#e5e7eb' ?>; color: <?= $i === $page ? 'white' : 'var(--text-primary)' ?>; border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
