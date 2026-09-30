<?php
require __DIR__ . '/../includes/auth.php';
// penghuni/edit.php
$base = '../';
$title = 'SIMKOS | Edit Penghuni';
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'];

// Ambil data penghuni + tipe kamar saat ini
$stmt = $pdo->prepare("SELECT p.*, k.tipe FROM penghuni p LEFT JOIN kamar k ON p.kamar_id = k.id WHERE p.id = :id");
$stmt->execute(['id' => $id]);
$penghuni = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil unique tipe kamar
$tipe_list = $pdo->query("SELECT DISTINCT tipe FROM kamar ORDER BY tipe ASC")->fetchAll(PDO::FETCH_COLUMN);

// Ambil kamar kosong + kamar yang sedang dipakai penghuni ini
$kamarStmt = $pdo->prepare("SELECT id, nomor_kamar, tipe, harga, status FROM kamar
                            WHERE status = 'KOSONG' OR id = :kamar_id
                            ORDER BY nomor_kamar ASC");
$kamarStmt->execute(['kamar_id' => $penghuni['kamar_id']]);
$kamarList = $kamarStmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<section class="card-section">
    <h2>Edit Penghuni</h2>
    <form action="proses_edit.php" method="POST" id="form-edit">
        <input type="hidden" name="id" value="<?= $penghuni['id'] ?>">
        <p>
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($penghuni['nama_lengkap']) ?>" required>
        </p>
        <p>
            <label for="no_hp">No. Handphone / WhatsApp</label>
            <input type="text" id="no_hp" name="no_hp" value="<?= htmlspecialchars($penghuni['no_hp']) ?>" required>
        </p>
        <p>
            <label for="tipe">Tipe Kamar</label>
            <select id="tipe" name="tipe" required>
                <option value="">-- Pilih Tipe Kamar --</option>
                <?php foreach ($tipe_list as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>" <?= ($penghuni['tipe'] ?? '') === $t ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="kamar_id">Pilih Kamar Kosong</label>
            <select id="kamar_id" name="kamar_id" required>
                <option value="">-- Pilih Kamar --</option>
                <?php foreach ($kamarList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $penghuni['kamar_id'] == $k['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nomor_kamar']) ?> - Rp <?= number_format($k['harga'], 0, ',', '.') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="tgl_masuk">Tanggal Masuk</label>
            <input type="date" id="tgl_masuk" name="tgl_masuk" value="<?= $penghuni['tgl_masuk'] ?>">
        </p>
        <p style="margin-top: 1.5rem;">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="list.php" style="margin-left: 12px; color: var(--text-muted);">Batal</a>
        </p>
    </form>
</section>

<script>
document.getElementById('tipe').addEventListener('change', function() {
    const tipe = this.value;
    const currentKamar = <?= (int)($penghuni['kamar_id'] ?? 0) ?>;
    const kamarSelect = document.getElementById('kamar_id');

    if (!tipe) {
        kamarSelect.innerHTML = '<option value="">-- Pilih Tipe Kamar Terlebih Dahulu --</option>';
        return;
    }

    fetch('get_kamar.php?tipe=' + encodeURIComponent(tipe))
        .then(res => res.json())
        .then(data => {
            kamarSelect.innerHTML = '<option value="">-- Pilih Kamar --</option>';
            if (data.length === 0) {
                kamarSelect.innerHTML += '<option disabled>Tidak ada kamar kosong</option>';
            } else {
                data.forEach(k => {
                    const opt = document.createElement('option');
                    opt.value = k.id;
                    opt.textContent = `${k.nomor_kamar} - Rp ${parseInt(k.harga).toLocaleString('id-ID')}`;
                    if (parseInt(k.id) === currentKamar) opt.selected = true;
                    kamarSelect.appendChild(opt);
                });
            }
        })
        .catch(err => console.error('Error:', err));
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>