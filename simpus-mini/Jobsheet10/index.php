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

<!-- Banner Welcome + Quick Action Buttons -->
<section class="card-section welcome-banner">
    <div>
        <h2>Selamat Datang di SIMKOS</h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.25rem;">
            Sistem Pengelolaan Data Kamar & Penghuni Kost Berbasis Web & PostgreSQL.
        </p>
    </div>
    <div class="quick-actions">
        <a href="kamar/tambah.php" class="btn-primary" style="font-size: 0.8rem; padding: 0.5rem 1rem;">+ Kamar Baru</a>
        <a href="penghuni/tambah.php" class="btn-secondary" style="font-size: 0.8rem; padding: 0.5rem 1rem;">+ Penghuni Baru</a>
    </div>
</section>

<!-- Card Stats Grid (Menjadi 4 Kartu) -->
<div class="grid-stats">
    <div class="stat-card">
        <h3>Total Kamar</h3>
        <div class="value"><?= $total_kamar ?></div>
    </div>
    <div class="stat-card">
        <h3>Kamar Terisi</h3>
        <div class="value" style="color: var(--warning-text);"><?= $kamar_terisi ?></div>
    </div>
    <div class="stat-card">
        <h3>Kamar Kosong</h3>
        <div class="value" style="color: var(--success-text);"><?= $kamar_kosong ?></div>
    </div>
    <div class="stat-card">
        <h3>Total Penghuni</h3>
        <div class="value" style="color: #2563eb;"><?= $total_penghuni ?></div>
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