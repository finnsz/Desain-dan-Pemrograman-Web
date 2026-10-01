<?php
require __DIR__ . '/../includes/auth.php';
$base = '../';
$title = 'Edit Pengeluaran';

require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    $_SESSION['flash_message'] = 'ID pengeluaran tidak valid.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pengeluaran WHERE id = :id");
$stmt->execute([':id' => $id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    $_SESSION['flash_message'] = 'Data pengeluaran tidak ditemukan.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: list.php');
    exit;
}
?>

<section class="card-section" style="max-width: 600px; margin: 0 auto;">
    <h2>Edit Pengeluaran</h2>

    <form method="POST" action="proses_edit.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">

        <div style="margin-bottom: 1.5rem;">
            <label for="kategori">Kategori</label>
            <select name="kategori" id="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Perbaikan" <?= $p['kategori'] === 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                <option value="Listrik" <?= $p['kategori'] === 'Listrik' ? 'selected' : '' ?>>Listrik</option>
                <option value="Air" <?= $p['kategori'] === 'Air' ? 'selected' : '' ?>>Air</option>
                <option value="Internet" <?= $p['kategori'] === 'Internet' ? 'selected' : '' ?>>Internet</option>
                <option value="Gaji" <?= $p['kategori'] === 'Gaji' ? 'selected' : '' ?>>Gaji</option>
                <option value="Lainnya" <?= $p['kategori'] === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
            </select>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="nominal">Nominal (Rp)</label>
            <input type="number" name="nominal" id="nominal" value="<?= (int)$p['nominal'] ?>" required>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="<?= $p['tanggal'] ?>" required>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; resize: vertical; height: 100px;"><?= htmlspecialchars($p['keterangan']) ?></textarea>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="bukti_path">Upload Bukti (Opsional)</label>
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

        <div style="display: flex; gap: 0.75rem;">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="list.php" style="padding: 0.75rem 1.75rem; background: #e5e7eb; color: var(--text-primary); border-radius: 10px; text-decoration: none; font-weight: 600;">Batal</a>
        </div>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
