<?php
// kamar/tambah.php
$base = '../';
$title = 'SIMKOS | Tambah Kamar';
include __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Tambah Kamar</h2>
    <form action="proses_tambah.php" method="POST" id="form-tambah">
        <p>
            <label for="nomor_kamar">Nomor Kamar</label><br>
            <input type="text" id="nomor_kamar" name="nomor_kamar" placeholder="Contoh: K03" required>
        </p>
        <p>
            <label for="tipe">Tipe Kamar</label><br>
            <input type="text" id="tipe" name="tipe" placeholder="Contoh: AC + KM Dalam" required>
        </p>
        <p>
            <label for="harga">Harga Per Bulan (Rp)</label><br>
            <input type="number" id="harga" name="harga" placeholder="1000000" required>
        </p>
        <p style="margin-top: 1.5rem;">
            <button type="submit">Simpan Kamar</button>
            <a href="list.php" style="margin-left: 12px; color: var(--text-muted);">Batal</a>
        </p>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>