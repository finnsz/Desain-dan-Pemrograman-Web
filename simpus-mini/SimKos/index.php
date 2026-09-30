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

// 2. Ambil 5 Data Penghuni Terbaru
$query_penghuni = "SELECT p.nama_lengkap, p.no_hp, p.tgl_masuk, k.nomor_kamar 
                  FROM penghuni p 
                  LEFT JOIN kamar k ON p.kamar_id = k.id 
                  ORDER BY p.id DESC LIMIT 5";
$penghuni_terbaru = $pdo->query($query_penghuni)->fetchAll();
?>

<!-- Welcome Banner -->
<section class="card-section welcome-banner">
    <div>
        <h2>Selamat Datang, <?= isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Admin' ?></h2>
        <p>Sistem Pengelolaan Data Kamar & Penghuni Kost Berbasis Web & PostgreSQL.</p>
    </div>
    <div class="quick-actions">
        <?php if ($sudahLogin): ?>
            <a href="kamar/tambah.php" class="btn-primary">+ Kamar Baru</a>
            <a href="penghuni/tambah.php" class="btn-secondary">+ Penghuni Baru</a>
        <?php else: ?>
            <a href="auth/login.php" class="btn-primary">Masuk Akun</a>
        <?php endif; ?>
    </div>
</section>

<!-- Card Stats Grid -->
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
            <div class="stat-icon stat-icon-orange"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="value"><?= $kamar_terisi ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <h3>Kamar Kosong</h3>
            <div class="stat-icon stat-icon-green"><i class="fas fa-unlock"></i></div>
        </div>
        <div class="value"><?= $kamar_kosong ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <h3>Total Penghuni</h3>
            <div class="stat-icon stat-icon-purple"><i class="fas fa-users"></i></div>
        </div>
        <div class="value"><?= $total_penghuni ?></div>
    </div>
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