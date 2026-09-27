<?php
require_once __DIR__ . '/../config/database.php';
$id = $_GET['id'];

// Ambil data penghuni
$stmt = $pdo->prepare("SELECT * FROM penghuni WHERE id = :id");
$stmt->execute(['id' => $id]);
$penghuni = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil data kamar
$kamarStmt = $pdo->query("SELECT id, nomor_kamar FROM kamar ORDER BY nomor_kamar ASC");
$kamarList = $kamarStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<form action="proses_edit.php" method="POST">
    <input type="hidden" name="id" value="<?= $penghuni['id'] ?>">
    Nama: <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($penghuni['nama_lengkap']) ?>"><br>
    No HP: <input type="text" name="no_hp" value="<?= htmlspecialchars($penghuni['no_hp']) ?>"><br>
    Kamar: 
    <select name="kamar_id">
        <option value="">- Pilih Kamar -</option>
        <?php foreach ($kamarList as $k): ?>
            <option value="<?= $k['id'] ?>" <?= $penghuni['kamar_id'] == $k['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($k['nomor_kamar']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    Tanggal Masuk: <input type="date" name="tgl_masuk" value="<?= $penghuni['tgl_masuk'] ?>"><br>
    <button type="submit">Simpan</button>
</form>