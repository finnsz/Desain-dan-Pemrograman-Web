<?php
require __DIR__ . '/../includes/auth.php';
// penghuni/tambah.php
$base = '../';
$title = 'SIMKOS | Tambah Penghuni';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

// Ambil unique tipe kamar
$tipe_list = $pdo->query("SELECT DISTINCT tipe FROM kamar ORDER BY tipe ASC")->fetchAll(PDO::FETCH_COLUMN);
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
            <label for="tipe">Pilih Tipe Kamar</label>
            <select id="tipe" name="tipe" required>
                <option value="">-- Pilih Tipe Kamar --</option>
                <?php foreach ($tipe_list as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="kamar_id">Pilih Kamar Kosong</label>
            <select id="kamar_id" name="kamar_id" required>
                <option value="">-- Pilih Tipe Kamar Terlebih Dahulu --</option>
            </select>
        </p>
        <p style="margin-top: 1.5rem;">
            <button type="submit" class="btn-primary">Simpan Penghuni</button>
            <a href="list.php" style="margin-left: 12px; color: var(--text-muted);">Batal</a>
        </p>
    </form>
</section>

<script>
document.getElementById('tipe').addEventListener('change', function() {
    const tipe = this.value;
    const kamarSelect = document.getElementById('kamar_id');

    if (!tipe) {
        kamarSelect.innerHTML = '<option value="">-- Pilih Tipe Kamar Terlebih Dahulu --</option>';
        return;
    }

    // Fetch kamar kosong berdasarkan tipe
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
                    kamarSelect.appendChild(opt);
                });
            }
        })
        .catch(err => console.error('Error:', err));
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>