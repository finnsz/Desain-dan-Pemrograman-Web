<?php
$base = '../';
$title = 'SIMKOS | Edit Penghuni';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: list.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM penghuni WHERE id = ?");
$stmt->execute([$id]);
$penghuni = $stmt->fetch();

if (!$penghuni) {
    $_SESSION['flash_message'] = "Data penghuni tidak ditemukan!";
    $_SESSION['flash_type'] = "danger";
    header("Location: list.php");
    exit;
}

// Ambil opsi kamar yang KOSONG atau kamar yang saat ini sedang ditempati penghuni tersebut
$stmtKamar = $pdo->prepare("SELECT * FROM kamar WHERE status = 'KOSONG' OR id = ? ORDER BY nomor_kamar ASC");
$stmtKamar->execute([$penghuni['kamar_id']]);
$kamar_options = $stmtKamar->fetchAll();
?>

<section class="card-section">
    <h2>Edit Data Penghuni</h2>
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
            <label for="kamar_id">Pilih Kamar</label>
            <select id="kamar_id" name="kamar_id" required>
                <option value="">-- Pilih Kamar --</option>
                <?php foreach ($kamar_options as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $k['id'] == $penghuni['kamar_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nomor_kamar']) ?> - <?= htmlspecialchars($k['tipe']) ?> (<?= $k['status'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p style="margin-top: 1.5rem;">
            <button type="submit" class="btn-primary">Update Penghuni</button>
            <a href="list.php" style="margin-left: 12px; color: var(--text-muted);">Batal</a>
        </p>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>