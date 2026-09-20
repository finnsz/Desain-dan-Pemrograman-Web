<?php
$base = '../';
$title = 'SIMPUS-Mini | Daftar Anggota';

require_once '../config/database.php';
include '../includes/header.php';

// Ambil data anggota dari PostgreSQL
$stmt = $pdo->query("SELECT * FROM anggota ORDER BY id DESC");
$anggota_list = $stmt->fetchAll();
?>

<section>
    <h2>Daftar Anggota</h2>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Anggota</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. HP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($anggota_list)): ?>
                    <tr><td colspan="4">Belum ada data anggota di database.</td></tr>
                <?php else: ?>
                    <?php foreach ($anggota_list as $anggota): ?>
                        <tr>
                            <td><?= htmlspecialchars($anggota['no_anggota']) ?></td>
                            <td><?= htmlspecialchars($anggota['nama']) ?></td>
                            <td><?= htmlspecialchars($anggota['alamat'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($anggota['no_hp'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include '../includes/footer.php'; ?>