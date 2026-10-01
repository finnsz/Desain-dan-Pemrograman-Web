<?php
$base = './';
$title = 'Beranda Overview';

require_once 'config/database.php';
include 'includes/header.php';

// 1. Ambil Data Statistik untuk Card
$total_kamar    = $pdo->query("SELECT COUNT(*) FROM kamar")->fetchColumn();
$kamar_terisi   = $pdo->query("SELECT COUNT(*) FROM kamar WHERE status = 'TERISI'")->fetchColumn();
$kamar_kosong   = $pdo->query("SELECT COUNT(*) FROM kamar WHERE status = 'KOSONG'")->fetchColumn();
$total_penghuni = $pdo->query("SELECT COUNT(*) FROM penghuni")->fetchColumn();

// 2. Keuangan: pemasukan tahun ini (LUNAS berdasarkan tanggal bayar)
$tahun_ini = (int)date('Y');

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(total_bayar), 0) as total
    FROM pembayaran
    WHERE EXTRACT(YEAR FROM tgl_bayar) = :tahun
      AND status = 'LUNAS'
");
$stmt->execute([':tahun' => $tahun_ini]);
$pemasukan_tahun_ini = $stmt->fetchColumn();

$stmt_tunggakan = $pdo->prepare("
    SELECT COUNT(*) as jumlah, COALESCE(SUM(total_bayar), 0) as nominal
    FROM v_pembayaran_detail WHERE is_terlambat = true AND status != 'LUNAS'
");
$stmt_tunggakan->execute();
$tunggakan = $stmt_tunggakan->fetch(PDO::FETCH_ASSOC);

// 3. Ambil 5 Data Penghuni Terbaru
$query_penghuni = "SELECT p.nama_lengkap, p.no_hp, p.tgl_masuk, k.nomor_kamar
                  FROM penghuni p
                  LEFT JOIN kamar k ON p.kamar_id = k.id
                  ORDER BY p.id DESC LIMIT 5";
$penghuni_terbaru = $pdo->query($query_penghuni)->fetchAll();

// 4. Occupancy rate
$occupancy_rate = $total_kamar > 0 ? round(($kamar_terisi / $total_kamar) * 100, 1) : 0;
?>

<!-- Welcome Banner dengan Occupancy Badge -->
<section class="card-section welcome-banner">
    <div>
        <h2>Selamat Datang, <?= isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Admin' ?></h2>
        <p>Sistem Pengelolaan Data Kamar & Penghuni Kost Berbasis Web & PostgreSQL.</p>
    </div>
    <div class="quick-actions">
        <span style="background: #D8EB13; color: #111827; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.875rem; display: inline-block;">
            <i class="fas fa-chart-pie"></i> Occupancy: <?= $occupancy_rate ?>%
        </span>
        <?php if ($sudahLogin): ?>
            <a href="kamar/tambah.php" class="btn-primary">+ Kamar Baru</a>
            <a href="penghuni/tambah.php" class="btn-secondary">+ Penghuni Baru</a>
        <?php else: ?>
            <a href="auth/login.php" class="btn-primary">Masuk Akun</a>
        <?php endif; ?>
    </div>
</section>

<!-- Card Stats Grid - Semua Icon Biru -->
<div class="grid-stats">
    <div class="stat-card">
        <div class="stat-header">
            <h3>Total Kamar</h3>
            <div class="stat-icon stat-icon-blue"><i class="fas fa-door-open"></i></div>
        </div>
        <div class="value"><?= $total_kamar ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <h3>Kamar Terisi</h3>
            <div class="stat-icon stat-icon-blue"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="value"><?= $kamar_terisi ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <h3>Kamar Kosong</h3>
            <div class="stat-icon stat-icon-blue"><i class="fas fa-unlock"></i></div>
        </div>
        <div class="value"><?= $kamar_kosong ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <h3>Total Penghuni</h3>
            <div class="stat-icon stat-icon-blue"><i class="fas fa-users"></i></div>
        </div>
        <div class="value"><?= $total_penghuni ?></div>
    </div>
</div>

<!-- Financial Metrics Cards -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Pemasukan Tahun Ini -->
    <section class="card-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1rem;">Pemasukan Tahun Ini</h3>
            <div class="stat-icon stat-icon-blue" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                <i class="fas fa-arrow-up" style="font-size: 1rem;"></i>
            </div>
        </div>
        <div style="font-size: 1.75rem; font-weight: 700; color: #047857; margin-bottom: 0.5rem;">
            Rp <?= number_format($pemasukan_tahun_ini, 0, ',', '.') ?>
        </div>
        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">
            Pembayaran lunas tahun ini
        </div>
        <a href="pembayaran/list.php" style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">
            Lihat Detail →
        </a>
    </section>

    <!-- Tunggakan -->
    <section class="card-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1rem;">Total Tunggakan</h3>
            <div style="background: var(--danger-bg); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                <i class="fas fa-exclamation-circle" style="font-size: 1rem; color: var(--danger-text);"></i>
            </div>
        </div>
        <div style="font-size: 1.75rem; font-weight: 700; color: var(--danger-text); margin-bottom: 0.5rem;">
            Rp <?= number_format($tunggakan['nominal'], 0, ',', '.') ?>
        </div>
        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">
            <?= $tunggakan['jumlah'] ?> pembayaran terlambat
        </div>
        <a href="pembayaran/list.php?filter_status=TERLAMBAT" style="font-size: 0.875rem; color: var(--primary); font-weight: 600;">
            Tindak Lanjuti →
        </a>
    </section>
</div>

<!-- Tabel Ringkasan Penghuni Terbaru -->
<section class="card-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h2>Penghuni Terbaru</h2>
        <a href="penghuni/list.php" style="font-size: 0.85rem; font-weight: 600;">Lihat Semua ➔</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Penghuni</th>
                    <th>No. Kamar</th>
                    <th>No. HP / WhatsApp</th>
                    <th>Tanggal Masuk</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($penghuni_terbaru)): ?>
                    <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">Belum ada data penghuni.</td></tr>
                <?php else: ?>
                    <?php foreach ($penghuni_terbaru as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['nama_lengkap']) ?></strong></td>
                            <td>
                                <?php if (!empty($p['nomor_kamar'])): ?>
                                    <span class="badge badge-warning"><?= htmlspecialchars($p['nomor_kamar']) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p['no_hp']) ?></td>
                            <td><?= date('d M Y', strtotime($p['tgl_masuk'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include 'includes/footer.php'; ?>