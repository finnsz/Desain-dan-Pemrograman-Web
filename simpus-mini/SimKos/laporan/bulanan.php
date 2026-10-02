<?php
require __DIR__ . '/../includes/auth.php';
$base = '../';
$title = 'Laporan Keuangan';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

$tipe_laporan = isset($_GET['tipe_laporan']) ? $_GET['tipe_laporan'] : 'seumur_hidup';
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

$bulan_nama = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Ambil tahun yang tersedia
$tahun_list = $pdo->query("
    SELECT DISTINCT periode_tahun FROM pembayaran
    UNION
    SELECT DISTINCT EXTRACT(YEAR FROM tanggal)::int FROM pengeluaran
    ORDER BY 1 DESC
")->fetchAll(PDO::FETCH_COLUMN);

// Pemasukan: pembayaran yang LUNAS
$where_pemasukan = "WHERE status = 'LUNAS'";
$params_pemasukan = [];
if ($tipe_laporan === 'bulanan') {
    $where_pemasukan .= " AND periode_bulan = :bulan AND periode_tahun = :tahun";
    $params_pemasukan = [':bulan' => $bulan, ':tahun' => $tahun];
} elseif ($tipe_laporan === 'tahunan') {
    $where_pemasukan .= " AND periode_tahun = :tahun";
    $params_pemasukan = [':tahun' => $tahun];
}

$stmt_pemasukan = $pdo->prepare("
    SELECT
        COALESCE(SUM(total_bayar), 0) as total_lunas,
        COUNT(*) as jumlah_pembayaran
    FROM pembayaran
    $where_pemasukan
");
$stmt_pemasukan->execute($params_pemasukan);
$pemasukan = $stmt_pemasukan->fetch(PDO::FETCH_ASSOC);

// Pengeluaran per kategori
$where_pengeluaran = "WHERE 1=1";
$params_pengeluaran = [];
if ($tipe_laporan === 'bulanan') {
    $where_pengeluaran .= " AND EXTRACT(MONTH FROM tanggal) = :bulan AND EXTRACT(YEAR FROM tanggal) = :tahun";
    $params_pengeluaran = [':bulan' => $bulan, ':tahun' => $tahun];
} elseif ($tipe_laporan === 'tahunan') {
    $where_pengeluaran .= " AND EXTRACT(YEAR FROM tanggal) = :tahun";
    $params_pengeluaran = [':tahun' => $tahun];
}

$stmt_pengeluaran = $pdo->prepare("
    SELECT kategori, COUNT(*) as jumlah, COALESCE(SUM(nominal), 0) as total
    FROM pengeluaran
    $where_pengeluaran
    GROUP BY kategori
    ORDER BY total DESC
");
$stmt_pengeluaran->execute($params_pengeluaran);
$pengeluaran_detail = $stmt_pengeluaran->fetchAll(PDO::FETCH_ASSOC);

// Total pengeluaran
$total_pengeluaran = array_sum(array_column($pengeluaran_detail, 'total'));

// Pembayaran belum lunas
$where_belum = "WHERE status != 'LUNAS'";
$params_belum = [];
if ($tipe_laporan === 'bulanan') {
    $where_belum .= " AND periode_bulan = :bulan AND periode_tahun = :tahun";
    $params_belum = [':bulan' => $bulan, ':tahun' => $tahun];
} elseif ($tipe_laporan === 'tahunan') {
    $where_belum .= " AND periode_tahun = :tahun";
    $params_belum = [':tahun' => $tahun];
}

$stmt_belum = $pdo->prepare("
    SELECT COUNT(*) FROM pembayaran
    $where_belum
");
$stmt_belum->execute($params_belum);
$pembayaran_belum = $stmt_belum->fetchColumn();

// Tunggakan (pembayaran yang jatuh tempo sebelum hari ini dan belum lunas)
$stmt_tunggakan = $pdo->prepare("
    SELECT COUNT(*) as jumlah, COALESCE(SUM(total_bayar), 0) as nominal
    FROM v_pembayaran_detail
    WHERE is_terlambat = true AND status != 'LUNAS'
");
$stmt_tunggakan->execute();
$tunggakan = $stmt_tunggakan->fetch(PDO::FETCH_ASSOC);

$laba_rugi = $pemasukan['total_lunas'] - $total_pengeluaran;
?>

<section class="card-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Laporan Keuangan</h2>
        <button onclick="window.print()" class="btn-primary" style="font-size: 0.875rem; padding: 0.6rem 1.25rem;">
            <i class="fas fa-print"></i> Print / PDF
        </button>
    </div>

    <!-- Filter Periode -->
    <form method="GET" style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem;">
        <select name="tipe_laporan" id="tipeLaporan" onchange="togglePeriod()">
            <option value="seumur_hidup" <?= $tipe_laporan === 'seumur_hidup' ? 'selected' : '' ?>>Seumur Hidup</option>
            <option value="tahunan" <?= $tipe_laporan === 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
            <option value="bulanan" <?= $tipe_laporan === 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
        </select>

        <select name="bulan" id="bulanSelect" style="<?= $tipe_laporan !== 'bulanan' ? 'display:none;' : '' ?>">
            <?php foreach ($bulan_nama as $num => $nama): ?>
                <option value="<?= $num ?>" <?= $num === $bulan ? 'selected' : '' ?>><?= $nama ?></option>
            <?php endforeach; ?>
        </select>

        <select name="tahun" id="tahunSelect" style="<?= $tipe_laporan === 'seumur_hidup' ? 'display:none;' : '' ?>">
            <?php foreach ($tahun_list as $t): ?>
                <option value="<?= $t ?>" <?= $t === $tahun ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-primary">Filter</button>
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

    <!-- Header Laporan -->
    <div style="text-align: center; padding: 1.5rem; border-bottom: 2px solid var(--border-color); margin-bottom: 2rem;">
        <h3 style="margin: 0 0 0.5rem 0;">LAPORAN KEUANGAN</h3>
        <p style="margin: 0; color: var(--text-secondary);">
            Periode:
            <?php
                if ($tipe_laporan === 'bulanan') {
                    echo $bulan_nama[$bulan] . ' ' . $tahun;
                } elseif ($tipe_laporan === 'tahunan') {
                    echo $tahun;
                } else {
                    echo 'Seumur Hidup';
                }
            ?>
        </p>
    </div>

    <!-- Ringkasan Pemasukan -->
    <div style="margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem; border-bottom: 2px solid var(--primary); padding-bottom: 0.5rem;">
            <i class="fas fa-arrow-up" style="color: var(--success-text);"></i> PEMASUKAN
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: var(--success-bg); padding: 1rem; border-radius: 10px;">
                <div style="font-size: 0.875rem; color: var(--success-text); margin-bottom: 0.5rem;">
                    Pembayaran Lunas
                </div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--success-text);">
                    Rp <?= number_format($pemasukan['total_lunas'], 0, ',', '.') ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--success-text); margin-top: 0.5rem;">
                    <?= $pemasukan['jumlah_pembayaran'] ?> pembayaran
                </div>
            </div>

            <div style="background: var(--warning-bg); padding: 1rem; border-radius: 10px;">
                <div style="font-size: 0.875rem; color: var(--warning-text); margin-bottom: 0.5rem;">
                    Pembayaran Belum Lunas
                </div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--warning-text);">
                    <?= $pembayaran_belum ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--warning-text); margin-top: 0.5rem;">
                    tagihan pending
                </div>
            </div>

            <div style="background: var(--danger-bg); padding: 1rem; border-radius: 10px;">
                <div style="font-size: 0.875rem; color: var(--danger-text); margin-bottom: 0.5rem;">
                    Total Tunggakan
                </div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--danger-text);">
                    Rp <?= number_format($tunggakan['nominal'], 0, ',', '.') ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--danger-text); margin-top: 0.5rem;">
                    <?= $tunggakan['jumlah'] ?> tagihan terlambat
                </div>
            </div>
        </div>
    </div>

    <!-- Rincian Pengeluaran -->
    <div style="margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem; border-bottom: 2px solid var(--danger-bg); padding-bottom: 0.5rem;">
            <i class="fas fa-arrow-down" style="color: var(--danger-text);"></i> PENGELUARAN
        </h3>

        <?php if (empty($pengeluaran_detail)): ?>
            <p style="color: var(--text-muted); text-align: center; padding: 2rem;">Tidak ada pengeluaran di bulan ini.</p>
        <?php else: ?>
            <div class="table-responsive" style="margin-bottom: 1rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Jumlah Item</th>
                            <th>Nominal</th>
                            <th>% dari Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pengeluaran_detail as $p): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($p['kategori']) ?></strong></td>
                                <td><?= $p['jumlah'] ?></td>
                                <td>Rp <?= number_format($p['total'], 0, ',', '.') ?></td>
                                <td><?= $total_pengeluaran > 0 ? round(($p['total'] / $total_pengeluaran) * 100, 1) : 0 ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="background: var(--danger-bg); padding: 1rem; border-radius: 10px; text-align: center;">
                <div style="font-size: 0.875rem; color: var(--danger-text); margin-bottom: 0.5rem;">
                    Total Pengeluaran
                </div>
                <div style="font-size: 1.8rem; font-weight: 700; color: var(--danger-text);">
                    Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ringkasan Laba/Rugi -->
    <div style="background: <?= $laba_rugi >= 0 ? 'var(--success-bg)' : 'var(--danger-bg)' ?>; padding: 2rem; border-radius: 12px; text-align: center;">
        <div style="font-size: 1rem; color: <?= $laba_rugi >= 0 ? 'var(--success-text)' : 'var(--danger-text)' ?>; margin-bottom: 1rem;">
            <strong><?= $laba_rugi >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' ?></strong>
        </div>
        <div style="font-size: 2.5rem; font-weight: 700; color: <?= $laba_rugi >= 0 ? 'var(--success-text)' : 'var(--danger-text)' ?>;">
            Rp <?= number_format(abs($laba_rugi), 0, ',', '.') ?>
        </div>
        <div style="font-size: 0.875rem; color: <?= $laba_rugi >= 0 ? 'var(--success-text)' : 'var(--danger-text)' ?>; margin-top: 1rem;">
            Pemasukan: Rp <?= number_format($pemasukan['total_lunas'], 0, ',', '.') ?> −
            Pengeluaran: Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?>
        </div>
    </div>

    <!-- Link ke laporan detail -->
    <div style="margin-top: 2rem; display: flex; gap: 0.75rem;">
        <a href="../pembayaran/list.php?tipe_laporan=<?= $tipe_laporan ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-primary" style="font-size: 0.875rem; padding: 0.6rem 1.25rem;">
            <i class="fas fa-receipt"></i> Lihat Detail Pembayaran
        </a>
        <a href="../pengeluaran/list.php?tipe_laporan=<?= $tipe_laporan ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-primary" style="font-size: 0.875rem; padding: 0.6rem 1.25rem;">
            <i class="fas fa-shopping-cart"></i> Lihat Detail Pengeluaran
        </a>
    </div>
</section>

<style>
    @media print {
        .btn-primary, button { display: none; }
        form { display: none; }
        .card-section { box-shadow: none; }
    }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
