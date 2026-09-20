<?php
$base = '../';
$title = 'SIMPUS-Mini | Tambah Buku';
include '../includes/header.php';
?>

<section>
    <h2>Tambah Buku</h2>
    <form action="proses_tambah.php" method="POST">
        <p>
            <label for="judul">Judul</label><br>
            <input type="text" id="judul" name="judul" required>
        </p>
        <p>
            <label for="pengarang">Pengarang</label><br>
            <input type="text" id="pengarang" name="pengarang" required>
        </p>
        <p>
            <label for="tahun">Tahun Terbit</label><br>
            <input type="number" id="tahun" name="tahun" required>
        </p>
        <p>
            <label for="isbn">ISBN</label><br>
            <input type="text" id="isbn" name="isbn">
        </p>
        <p>
            <label for="stok">Stok</label><br>
            <input type="number" id="stok" name="stok" required>
        </p>
        <p>
            <label for="kategori">Kategori</label><br>
            <select id="kategori" name="kategori">
                <option value="Fiksi">Fiksi</option>
                <option value="Non-Fiksi">Non-Fiksi</option>
                <option value="Referensi">Referensi</option>
            </select>
        </p>
        <p>
            <button type="submit">Simpan</button>
        </p>
    </form>
</section>

<?php include '../includes/footer.php'; ?>