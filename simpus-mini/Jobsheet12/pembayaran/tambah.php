<?php
require __DIR__ . '/../includes/auth.php';
$base = '../';
$title = 'Catat Pembayaran';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

// Ambil daftar penghuni + info kamar mereka
$penghuni = $pdo->query("
    SELECT p.id, p.nama_lengkap, p.no_hp, k.nomor_kamar, k.tipe, k.harga
    FROM penghuni p
    LEFT JOIN kamar k ON p.kamar_id = k.id
    ORDER BY p.nama_lengkap
")->fetchAll(PDO::FETCH_ASSOC);

$bulan_nama = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$tahun_current = date('Y');
?>

<section class="card-section" style="max-width: 600px; margin: 0 auto;">
    <h2>Catat Pembayaran Sewa</h2>

    <form method="POST" action="proses_tambah.php" enctype="multipart/form-data">
        <div style="margin-bottom: 1.5rem;">
            <label for="penghuni_id">Penghuni</label>
            <select name="penghuni_id" id="penghuni_id" required>
                <option value="">-- Pilih Penghuni --</option>
                <?php foreach ($penghuni as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['nama_lengkap']) ?> - Kamar <?= htmlspecialchars($p['nomor_kamar']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div id="penghuni-info" style="margin-top: 1rem; padding: 1rem; background: #f9fafb; border-radius: 8px; display: none;">
                <p style="margin: 0; font-size: 0.875rem;"><strong>Harga Sewa:</strong> <span id="harga-sewa">-</span></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label for="periode_bulan">Bulan</label>
                <select name="periode_bulan" id="periode_bulan" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach ($bulan_nama as $num => $nama): ?>
                        <option value="<?= $num ?>" <?= $num === (int)date('m') ? 'selected' : '' ?>><?= $nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="periode_tahun">Tahun</label>
                <input type="number" name="periode_tahun" id="periode_tahun" value="<?= $tahun_current ?>" required>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="nominal">Nominal Sewa (Rp)</label>
            <input type="number" name="nominal" id="nominal" placeholder="0" required>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="tgl_jatuh_tempo">Tanggal Jatuh Tempo</label>
            <input type="date" name="tgl_jatuh_tempo" id="tgl_jatuh_tempo" required>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="status">Status</label>
            <select name="status" id="status" required onchange="toggleBayarFields()">
                <option value="BELUM BAYAR">Belum Bayar</option>
                <option value="LUNAS">Lunas</option>
            </select>
        </div>

        <div id="bayar-fields" style="display: none;">
            <div style="margin-bottom: 1.5rem;">
                <label for="tgl_bayar">Tanggal Pembayaran</label>
                <input type="date" name="tgl_bayar" id="tgl_bayar">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="metode_bayar">Metode Pembayaran</label>
                <select name="metode_bayar" id="metode_bayar" onchange="toggleBuktiTransfer()">
                    <option value="">-- Pilih --</option>
                    <option value="Cash">Cash</option>
                    <option value="Transfer">Transfer Bank</option>
                    <option value="E-Wallet">E-Wallet</option>
                </select>
            </div>

            <div id="bukti-field" style="margin-bottom: 1.5rem; display: none;">
                <label for="bukti_path">Upload Bukti Transfer</label>
                <input type="file" name="bukti_path" id="bukti_path" accept=".pdf,.jpg,.jpeg,.png" style="max-width: 100%; padding: 0.75rem; border: 2px dashed var(--border-color); border-radius: 8px; cursor: pointer;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Format: PDF, JPG, PNG (max 2MB)</p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="denda">Denda Keterlambatan (Rp)</label>
                <input type="number" name="denda" id="denda" placeholder="0" value="0" readonly style="background: #f9fafb;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Otomatis: Rp 50.000 per hari keterlambatan</p>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan" placeholder="Catatan tambahan..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; resize: vertical; height: 100px;"></textarea>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" class="btn-primary">Simpan Pembayaran</button>
            <a href="list.php" style="padding: 0.75rem 1.75rem; background: #e5e7eb; color: var(--text-primary); border-radius: 10px; text-decoration: none; font-weight: 600;">Batal</a>
        </div>
    </form>
</section>

<script>
const penghuni_select = document.getElementById('penghuni_id');
const penghuni_info = document.getElementById('penghuni-info');
const harga_sewa = document.getElementById('harga-sewa');
const nominal_input = document.getElementById('nominal');
const status_select = document.getElementById('status');
const tgl_bayar_input = document.getElementById('tgl_bayar');
const tgl_jatuh_tempo_input = document.getElementById('tgl_jatuh_tempo');
const denda_input = document.getElementById('denda');
const metode_bayar_select = document.getElementById('metode_bayar');
const bukti_field = document.getElementById('bukti-field');

const penghuni_data = <?= json_encode(array_column($penghuni, null, 'id')) ?>;

penghuni_select.addEventListener('change', function() {
    if (this.value && penghuni_data[this.value]) {
        const data = penghuni_data[this.value];
        harga_sewa.textContent = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(data.harga || 0);
        nominal_input.value = data.harga || 0;
        penghuni_info.style.display = 'block';
    } else {
        penghuni_info.style.display = 'none';
        nominal_input.value = '';
    }
});

function toggleBayarFields() {
    const bayar_fields = document.getElementById('bayar-fields');
    if (status_select.value === 'LUNAS') {
        bayar_fields.style.display = 'block';
        document.getElementById('tgl_bayar').required = true;
        document.getElementById('metode_bayar').required = true;

        // Auto-fill tgl_bayar dengan hari ini jika kosong
        if (!tgl_bayar_input.value) {
            const today = new Date().toISOString().split('T')[0];
            tgl_bayar_input.value = today;
        }

        // Set default metode_bayar ke Cash jika kosong
        if (!metode_bayar_select.value) {
            metode_bayar_select.value = 'Cash';
            toggleBuktiTransfer();
        }

        calculateDenda();
    } else {
        bayar_fields.style.display = 'none';
        document.getElementById('tgl_bayar').required = false;
        document.getElementById('metode_bayar').required = false;
        denda_input.value = 0;
    }
}

function toggleBuktiTransfer() {
    if (metode_bayar_select.value === 'Cash') {
        bukti_field.style.display = 'none';
        document.getElementById('bukti_path').required = false;
    } else {
        bukti_field.style.display = 'block';
    }
}

function calculateDenda() {
    const tgl_bayar = tgl_bayar_input.value;
    const tgl_jatuh_tempo = tgl_jatuh_tempo_input.value;

    if (tgl_bayar && tgl_jatuh_tempo && status_select.value === 'LUNAS') {
        const bayar = new Date(tgl_bayar);
        const tempo = new Date(tgl_jatuh_tempo);

        if (bayar > tempo) {
            const diff_time = bayar - tempo;
            const diff_days = Math.ceil(diff_time / (1000 * 60 * 60 * 24));
            let denda = diff_days * 50000;
            if (denda > 500000) denda = 500000;
            denda_input.value = denda;
        } else {
            denda_input.value = 0;
        }
    }
}

tgl_bayar_input.addEventListener('change', calculateDenda);
tgl_jatuh_tempo_input.addEventListener('change', calculateDenda);

// Set default jatuh tempo ke 15 hari setelah akhir bulan
document.getElementById('periode_bulan').addEventListener('change', updateJatuhTempo);
document.getElementById('periode_tahun').addEventListener('change', updateJatuhTempo);

function updateJatuhTempo() {
    const bulan = parseInt(document.getElementById('periode_bulan').value);
    const tahun = parseInt(document.getElementById('periode_tahun').value);
    if (bulan && tahun) {
        // Akhir bulan = new Date(tahun, bulan, 0) = hari terakhir bulan tersebut
        const akhir_bulan = new Date(tahun, bulan, 0);
        // Tambah 15 hari
        const jatuh_tempo = new Date(akhir_bulan);
        jatuh_tempo.setDate(akhir_bulan.getDate() + 15);
        const iso = jatuh_tempo.toISOString().split('T')[0];
        document.getElementById('tgl_jatuh_tempo').value = iso;
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
