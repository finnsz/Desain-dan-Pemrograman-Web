<?php
$base = '../';
$title = 'SIMKOS | Edit Kamar';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: list.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM kamar WHERE id = ?");
$stmt->execute([$id]);
$kamar = $stmt->fetch();

if (!$kamar) {
    $_SESSION['flash_message'] = "Data kamar tidak ditemukan!";
    $_SESSION['flash_type'] = "danger";
    header("Location: list.php");
    exit;
}
?>

<section class="card-section">
    <h2>Edit Data Kamar</h2>
    <form action="proses_edit.php" method="POST" id="form-edit">
        <input type="hidden" name="id" value="<?= $kamar['id'] ?>">
        <p>
            <label for="nomor_kamar">Nomor Kamar</label><br>
            <input type="text" id="nomor_kamar" name="nomor_kamar" value="<?= htmlspecialchars($kamar['nomor_kamar']) ?>" required>
        </p>
        <p>
            <label for="tipe">Tipe Kamar</label><br>
            <input type="text" id="tipe" name="tipe" value="<?= htmlspecialchars($kamar['tipe']) ?>" required>
        </p>
        <p>
            <label for="harga">Harga Per Bulan (Rp)</label><br>
            <input type="number" id="harga" name="harga" value="<?= $kamar['harga'] ?>" required>
        </p>
        <p>
            <label for="status">Status Kamar</label><br>
            <select id="status" name="status" required>
                <option value="KOSONG" <?= $kamar['status'] === 'KOSONG' ? 'selected' : '' ?>>KOSONG</option>
                <option value="TERISI" <?= $kamar['status'] === 'TERISI' ? 'selected' : '' ?>>TERISI</option>
            </select>
        </p>
        <p style="margin-top: 1.5rem;">
            <button type="submit" class="btn-primary">Update Kamar</button>
            <a href="list.php" style="margin-left: 12px; color: var(--text-muted);">Batal</a>
        </p>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>