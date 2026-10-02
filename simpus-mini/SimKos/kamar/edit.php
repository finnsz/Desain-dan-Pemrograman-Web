<?php
require __DIR__ . '/../includes/auth.php';
// kamar/edit.php
$base = '../';
$title = 'SIMKOS | Edit Kamar';
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM kamar WHERE id = :id");
$stmt->execute(['id' => $id]);
$kamar = $stmt->fetch(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<section class="card-section">
    <h2>Edit Kamar</h2>
    <form action="proses_edit.php" method="POST" id="form-edit">
        <input type="hidden" name="id" value="<?php echo (int) $kamar['id']; ?>">
        <?php echo csrf_field(); ?>
        <p>
            <label for="nomor_kamar">Nomor Kamar</label>
            <input type="text" id="nomor_kamar" name="nomor_kamar" value="<?php echo e($kamar['nomor_kamar']); ?>" required>
        </p>
        <p>
            <label for="tipe">Tipe Kamar</label>
            <input type="text" id="tipe" name="tipe" value="<?php echo e($kamar['tipe']); ?>" required>
        </p>
        <p>
            <label for="harga">Harga Per Bulan (Rp)</label>
            <input type="number" id="harga" name="harga" value="<?= $kamar['harga'] ?>" required>
        </p>
        <p>
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="KOSONG" <?= $kamar['status'] == 'KOSONG' ? 'selected' : '' ?>>KOSONG</option>
                <option value="TERISI" <?= $kamar['status'] == 'TERISI' ? 'selected' : '' ?>>TERISI</option>
            </select>
        </p>
        <p style="margin-top: 1.5rem;">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="list.php" style="margin-left: 12px; color: var(--text-muted);">Batal</a>
        </p>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>