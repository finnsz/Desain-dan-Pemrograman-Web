<?php
// penghuni/tambah.php
$base = '../';
$title = 'SIMKOS | Tambah Penghuni';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

// Ambil list kamar yang masih KOSONG
$kamar_kosong = $pdo->query("SELECT * FROM kamar WHERE status = 'KOSONG' ORDER BY nomor_kamar ASC")->fetchAll();
?>

<section class="card-section">
    <h2>Tambah Penghuni Baru</h2>
    <form action="proses_tambah.php" method="POST" id="form-tambah">
        <p>
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
        </p>
        <p>
            <label for="no_hp">No. Handphone / WhatsApp</label>
            <input type="text" id="no_hp" name="no_hp" placeholder="08xxxxxxxxxx" required>
        </p>
        <p>
            <label for="kamar_id">Pilih Kamar (Status Kosong)</label>
            <select id="kamar_id" name="kamar_id" required>
                <option value="">-- Pilih Kamar --</option>
                <?php foreach ($kamar_kosong as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nomor_kamar']) ?> - <?= htmlspecialchars($k['tipe']) ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p style="margin-top: 1.5rem;">
            <button type="submit" class="btn-primary">Simpan Penghuni</button>
            <a href="list.php" style="margin-left: 12px; color: var(--text-muted);">Batal</a>
        </p>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>