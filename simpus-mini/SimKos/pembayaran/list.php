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

// Ambil data
$query = "SELECT * FROM v_pembayaran_detail
          $whereClause
          ORDER BY periode_tahun DESC, periode_bulan DESC, tgl_jatuh_tempo DESC
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
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / kamar..." style="max-width: 200px;">

        <select name="status" style="max-width: 150px;">
            <option value="">Semua Status</option>
            <option value="BELUM BAYAR" <?= $filter_status === 'BELUM BAYAR' ? 'selected' : '' ?>>Belum Bayar</option>
            <option value="LUNAS" <?= $filter_status === 'LUNAS' ? 'selected' : '' ?>>Lunas</option>
            <option value="TERLAMBAT" <?= $filter_status === 'TERLAMBAT' ? 'selected' : '' ?>>Terlambat</option>
        </select>

        <select name="bulan" style="max-width: 130px;">
            <option value="0">Semua Bulan</option>
            <?php foreach ($bulan_nama as $num => $nama): ?>
                <option value="<?= $num ?>" <?= $filter_bulan === $num ? 'selected' : '' ?>><?= $nama ?></option>
            <?php endforeach; ?>
        </select>

        <select name="tahun" style="max-width: 100px;">
            <option value="0">Semua Tahun</option>
            <?php foreach ($tahun_list as $tahun): ?>
                <option value="<?= $tahun ?>" <?= $filter_tahun === $tahun ? 'selected' : '' ?>><?= $tahun ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-primary" style="padding: 0.6rem 1.25rem; font-size: 0.875rem;">Filter</button>
        <?php if ($search || $filter_status || $filter_bulan || $filter_tahun): ?>
            <a href="list.php" style="padding: 0.6rem 0.75rem; background: var(--danger-bg); color: var(--danger-text); border-radius: 8px; font-size: 0.875rem; font-weight: 500;">Reset</a>
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
        <table>
            <thead>
                <tr>
                    <th>Penghuni</th>
                    <th>Kamar</th>
                    <th>Periode</th>
                    <th>Nominal</th>
                    <th>Denda</th>
                    <th>Total</th>
                    <th>Jatuh Tempo</th>
                    <th>Tgl Bayar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pembayaran_list)): ?>
                    <tr><td colspan="10" style="text-align: center; color: var(--text-muted);">Belum ada data pembayaran.</td></tr>
                <?php else: ?>
                    <?php foreach ($pembayaran_list as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['nama_lengkap']) ?></strong></td>
                            <td><span class="badge badge-warning"><?= htmlspecialchars($p['nomor_kamar']) ?></span></td>
                            <td><?= $bulan_nama[$p['periode_bulan']] ?> <?= $p['periode_tahun'] ?></td>
                            <td>Rp <?= number_format($p['nominal'], 0, ',', '.') ?></td>
                            <td><?= $p['denda'] > 0 ? 'Rp ' . number_format($p['denda'], 0, ',', '.') : '-' ?></td>
                            <td><strong>Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($p['tgl_jatuh_tempo'])) ?></td>
                            <td><?= $p['tgl_bayar'] ? date('d/m/Y', strtotime($p['tgl_bayar'])) : '-' ?></td>
                            <td>
                                <?php
                                $badge_class = 'badge-success';
                                $status = $p['status'];
                                if ($p['is_terlambat'] && $status !== 'LUNAS') {
                                    $badge_class = 'badge-danger';
                                    $status = 'TERLAMBAT';
                                } elseif ($status === 'BELUM BAYAR') {
                                    $badge_class = 'badge-warning';
                                }
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= $status ?></span>
                            </td>
                            <td>
                                <div class="aksi">
                                    <?php if ($p['status'] === 'LUNAS'): ?>
                                        <a href="kwitansi.php?id=<?= $p['id'] ?>" class="btn-edit" target="_blank">
                                            <i class="fas fa-print"></i> Kwitansi
                                        </a>
                                    <?php else: ?>
                                        <a href="edit.php?id=<?= $p['id'] ?>" class="btn-edit">Bayar</a>
                                    <?php endif; ?>
                                    <?php if (is_admin()): ?>
                                        <a href="edit.php?id=<?= $p['id'] ?>" class="btn-edit">Edit</a>
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
            <a href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&status=<?= urlencode($filter_status) ?>&bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>"
               style="padding: 0.5rem 0.75rem; background: <?= $i === $page ? 'var(--primary)' : '#e5e7eb' ?>; color: <?= $i === $page ? 'white' : 'var(--text-primary)' ?>; border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
