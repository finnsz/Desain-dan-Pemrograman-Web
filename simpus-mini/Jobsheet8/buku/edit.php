<?php
$base = '../';
$title = 'SIMPUS-Mini | Edit Buku';
include '../config/database.php';
include '../includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: list.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->execute([$id]);
$buku = $stmt->fetch();

if (!$buku) {
    $_SESSION['flash_message'] = "Data buku tidak ditemukan!";
    $_SESSION['flash_type'] = "danger";
    header("Location: list.php");
    exit;
}
?>

<section>
    <h2>Edit Buku</h2>
    <form action="proses_edit.php" method="POST">
        <input type="hidden" name="id" value="<?= $buku['id'] ?>">
        
        <p>
            <label for="judul">Judul</label><br>
            <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($buku['judul']) ?>" required>
        </p>
        <p>
            <label for="pengarang">Pengarang</label><br>
            <input type="text" id="pengarang" name="pengarang" value="<?= htmlspecialchars($buku['pengarang']) ?>" required>
        </p>
        <p>
            <label for="tahun">Tahun Terbit</label><br>
            <input type="number" id="tahun" name="tahun" value="<?= htmlspecialchars($buku['tahun']) ?>" required>
        </p>
        <p>
            <label for="isbn">ISBN</label><br>
            <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($buku['isbn']) ?>">
        </p>
        <p>
            <label for="stok">Stok</label><br>
            <input type="number" id="stok" name="stok" value="<?= htmlspecialchars($buku['stok']) ?>" required>
        </p>
        <p>
            <label for="kategori">Kategori</label><br>
            <select id="kategori" name="kategori">
                <option value="Fiksi" <?= $buku['kategori'] === 'Fiksi' ? 'selected' : '' ?>>Fiksi</option>
                <option value="Non-Fiksi" <?= $buku['kategori'] === 'Non-Fiksi' ? 'selected' : '' ?>>Non-Fiksi</option>
                <option value="Referensi" <?= $buku['kategori'] === 'Referensi' ? 'selected' : '' ?>>Referensi</option>
            </select>
        </p>
        <p>
            <button type="submit">Update Buku</button>
            <a href="list.php">Batal</a>
        </p>
    </form>
</section>

<?php include '../includes/footer.php'; ?>