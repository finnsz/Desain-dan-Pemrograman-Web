<?php
require_once '../config/koneksi.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM kamar WHERE id = :id");
$stmt->execute(['id' => $id]);
$kamar = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<form action="proses_edit.php" method="POST">
    <input type="hidden" name="id" value="<?= $kamar['id'] ?>">
    Nomor: <input type="text" name="nomor_kamar" value="<?= htmlspecialchars($kamar['nomor_kamar']) ?>"><br>
    Tipe: <input type="text" name="tipe" value="<?= htmlspecialchars($kamar['tipe']) ?>"><br>
    Harga: <input type="number" name="harga" value="<?= $kamar['harga'] ?>"><br>
    Status: 
    <select name="status">
        <option value="KOSONG" <?= $kamar['status'] == 'KOSONG' ? 'selected' : '' ?>>KOSONG</option>
        <option value="TERISI" <?= $kamar['status'] == 'TERISI' ? 'selected' : '' ?>>TERISI</option>
    </select><br>
    <button type="submit">Simpan</button>
</form>