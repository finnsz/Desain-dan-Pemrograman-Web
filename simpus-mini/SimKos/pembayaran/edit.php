<?php
require __DIR__ . '/../includes/auth.php';
$base = '../';
$title = 'Edit Pembayaran';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    $_SESSION['flash_message'] = 'ID pembayaran tidak valid.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM v_pembayaran_detail WHERE id = :id");
$stmt->execute([':id' => $id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    $_SESSION['flash_message'] = 'Data pembayaran tidak ditemukan.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: list.php');
    exit;
}

$bulan_nama = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<section class="card-section" style="max-width: 600px; margin: 0 auto;">
    <h2>Edit Pembayaran</h2>

    <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
        <p style="margin: 0 0 0.5rem 0; font-size: 0.875rem;"><strong><?= htmlspecialchars($p['nama_lengkap']) ?></strong></p>
        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">
            Kamar <?= htmlspecialchars($p['nomor_kamar']) ?> &middot;
            Periode <?= $bulan_nama[$p['periode_bulan']] ?> <?= $p['periode_tahun'] ?>
        </p>
    </div>

    <form method="POST" action="proses_edit.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">

        <div style="margin-bottom: 1.5rem;">
            <label for="nominal">Nominal Sewa (Rp)</label>
            <input type="number" name="nominal" id="nominal" value="<?= (int)$p['nominal'] ?>" required>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="tgl_jatuh_tempo">Tanggal Jatuh Tempo</label>
            <input type="date" name="tgl_jatuh_tempo" id="tgl_jatuh_tempo" value="<?= $p['tgl_jatuh_tempo'] ?>" required>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="status">Status</label>
            <select name="status" id="status" required onchange="toggleBayarFields()">
                <option value="BELUM BAYAR" <?= $p['status'] === 'BELUM BAYAR' ? 'selected' : '' ?>>Belum Bayar</option>
                <option value="LUNAS" <?= $p['status'] === 'LUNAS' ? 'selected' : '' ?>>Lunas</option>
            </select>
        </div>

        <div id="bayar-fields" style="display: <?= $p['status'] === 'LUNAS' ? 'block' : 'none' ?>;">
            <div style="margin-bottom: 1.5rem;">
                <label for="tgl_bayar">Tanggal Pembayaran</label>
                <input type="date" name="tgl_bayar" id="tgl_bayar" value="<?= $p['tgl_bayar'] ?>">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="metode_bayar">Metode Pembayaran</label>
                <select name="metode_bayar" id="metode_bayar">
                    <option value="">-- Pilih --</option>
                    <option value="Cash" <?= $p['metode_bayar'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
                    <option value="Transfer" <?= $p['metode_bayar'] === 'Transfer' ? 'selected' : '' ?>>Transfer Bank</option>
                    <option value="E-Wallet" <?= $p['metode_bayar'] === 'E-Wallet' ? 'selected' : '' ?>>E-Wallet</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="bukti_path">Upload Bukti Transfer</label>
                <?php if (!empty($p['bukti_path'])): ?>
                    <div style="margin-bottom: 0.75rem;">
                        <a href="<?= $base . htmlspecialchars($p['bukti_path']) ?>" target="_blank" style="font-size: 0.875rem;">
                            <i class="fas fa-file-alt"></i> Lihat bukti saat ini
                        </a>
                    </div>
                <?php endif; ?>
                <input type="file" name="bukti_path" id="bukti_path" accept=".pdf,.jpg,.jpeg,.png" style="max-width: 100%; padding: 0.75rem; border: 2px dashed var(--border-color); border-radius: 8px; cursor: pointer;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Kosongkan jika tidak ingin mengubah bukti</p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="denda">Denda Keterlambatan (Rp)</label>
                <input type="number" name="denda" id="denda" value="<?= (int)$p['denda'] ?>">
            </div>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="list.php" style="padding: 0.75rem 1.75rem; background: #e5e7eb; color: var(--text-primary); border-radius: 10px; text-decoration: none; font-weight: 600;">Batal</a>
        </div>
    </form>
</section>

<script>
const status_select = document.getElementById('status');

function toggleBayarFields() {
    const bayar_fields = document.getElementById('bayar-fields');
    if (status_select.value === 'LUNAS') {
        bayar_fields.style.display = 'block';
        document.getElementById('tgl_bayar').required = true;
        document.getElementById('metode_bayar').required = true;
    } else {
        bayar_fields.style.display = 'none';
        document.getElementById('tgl_bayar').required = false;
        document.getElementById('metode_bayar').required = false;
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>