<?php
require __DIR__ . '/../includes/auth.php';
$base = '../';
$title = 'Tambah Pengeluaran';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';
?>

<section class="card-section" style="max-width: 600px; margin: 0 auto;">
    <h2>Tambah Pengeluaran</h2>

    <form method="POST" action="proses_tambah.php" enctype="multipart/form-data">
        <div style="margin-bottom: 1.5rem;">
            <label for="kategori">Kategori</label>
            <select name="kategori" id="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Perbaikan">Perbaikan</option>
                <option value="Listrik">Listrik</option>
                <option value="Air">Air</option>
                <option value="Internet">Internet</option>
                <option value="Gaji">Gaji</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="nominal">Nominal (Rp)</label>
            <input type="number" name="nominal" id="nominal" placeholder="0" required>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan" placeholder="Deskripsi pengeluaran..." required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; resize: vertical; height: 100px;"></textarea>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="bukti_path">Upload Bukti (Opsional)</label>
            <input type="file" name="bukti_path" id="bukti_path" accept=".pdf,.jpg,.jpeg,.png" style="max-width: 100%; padding: 0.75rem; border: 2px dashed var(--border-color); border-radius: 8px; cursor: pointer;">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Format: PDF, JPG, PNG (max 2MB)</p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" class="btn-primary">Simpan Pengeluaran</button>
            <a href="list.php" style="padding: 0.75rem 1.75rem; background: #e5e7eb; color: var(--text-primary); border-radius: 10px; text-decoration: none; font-weight: 600;">Batal</a>
        </div>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
