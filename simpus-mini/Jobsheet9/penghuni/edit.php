<?php
// penghuni/edit.php
$base = '../';
$title = 'SIMKOS | Edit Penghuni';
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'];

// Ambil data penghuni
$stmt = $pdo->prepare("SELECT * FROM penghuni WHERE id = :id");
$stmt->execute(['id' => $id]);
$penghuni = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil data kamar
$kamarStmt = $pdo->query("SELECT id, nomor_kamar FROM kamar ORDER BY nomor_kamar ASC");
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
            <label for="kamar_id">Kamar</label>
            <select id="kamar_id" name="kamar_id">
                <option value="">- Pilih Kamar -</option>
                <?php foreach ($kamarList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $penghuni['kamar_id'] == $k['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nomor_kamar']) ?>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>