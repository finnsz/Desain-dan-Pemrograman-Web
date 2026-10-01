<?php
require __DIR__ . '/../includes/auth.php';
$base = '../';
$title = 'Generate Tagihan Otomatis';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/role.php';

// Hanya admin yang bisa generate
require_admin('list.php');

$bulan_nama = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$tahun_current = date('Y');
$bulan_current = (int)date('m');
?>

<section class="card-section" style="max-width: 600px; margin: 0 auto;">
    <h2>Generate Tagihan Otomatis</h2>
    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
        Buat tagihan pembayaran sewa untuk semua penghuni yang aktif pada periode tertentu.
    </p>

    <form method="POST" action="proses_generate.php">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label for="periode_bulan">Bulan</label>
                <select name="periode_bulan" id="periode_bulan" required>
                    <option value="">-- Pilih Bulan --</option>
                    <?php foreach ($bulan_nama as $num => $nama): ?>
                        <option value="<?= $num ?>" <?= $num === $bulan_current + 1 || ($bulan_current === 12 && $num === 1) ? 'selected' : '' ?>>
                            <?= $nama ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="periode_tahun">Tahun</label>
                <input type="number" name="periode_tahun" id="periode_tahun" value="<?= $bulan_current === 12 ? $tahun_current + 1 : $tahun_current ?>" required>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="tgl_jatuh_tempo">Tanggal Jatuh Tempo</label>
            <input type="date" name="tgl_jatuh_tempo" id="tgl_jatuh_tempo" required value="<?php
                $bulan_default = $bulan_current + 1;
                if ($bulan_default > 12) $bulan_default = 1;
                $tahun_default = $bulan_current === 12 ? $tahun_current + 1 : $tahun_current;
                // Akhir bulan + 15 hari
                $akhir_bulan = strtotime("last day of " . date('Y-m-d', mktime(0, 0, 0, $bulan_default, 1, $tahun_default)));
                $jatuh_tempo = strtotime('+15 days', $akhir_bulan);
                echo date('Y-m-d', $jatuh_tempo);
            ?>">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
                Default: 15 hari setelah akhir bulan tagihan
            </p>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="filter_status">Filter Penghuni Aktif (Status Kamar)</label>
            <select name="filter_status" id="filter_status">
                <option value="TERISI" selected>Hanya Kamar TERISI</option>
                <option value="">Semua Penghuni</option>
            </select>
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
                Rekomendasi: pilih "Hanya Kamar TERISI" untuk skip penghuni yang sudah keluar
            </p>
        </div>

        <div style="background: #f0f9ff; border: 1px solid #bfdbfe; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <p style="margin: 0; font-size: 0.875rem; color: #1e40af;">
                <i class="fas fa-info-circle"></i>
                <strong>Info:</strong> Tagihan untuk periode yang sudah ada tidak akan di-generate ulang.
                Jika ingin mengubah, hapus dulu atau gunakan Edit.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-magic"></i> Generate Tagihan
            </button>
            <a href="list.php" style="padding: 0.75rem 1.75rem; background: #e5e7eb; color: var(--text-primary); border-radius: 10px; text-decoration: none; font-weight: 600;">Batal</a>
        </div>
    </form>
</section>

<script>
document.getElementById('periode_bulan').addEventListener('change', updateJatuhTempo);
document.getElementById('periode_tahun').addEventListener('change', updateJatuhTempo);

function updateJatuhTempo() {
    const bulan = parseInt(document.getElementById('periode_bulan').value);
    const tahun = parseInt(document.getElementById('periode_tahun').value);
    if (bulan && tahun) {
        // Akhir bulan = last day of month
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
