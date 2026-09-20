<?php
$base = '../';
$title = 'SIMPUS-Mini | Tambah Anggota';
include '../includes/header.php';
?>

<section>
    <h2>Tambah Anggota</h2>
    <form action="proses_tambah.php" method="POST">
        <p>
            <label for="no_anggota">No. Anggota</label><br>
            <input type="text" id="no_anggota" name="no_anggota" required>
        </p>
        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" required>
        </p>
        <p>
            <label for="alamat">Alamat</label><br>
            <input type="text" id="alamat" name="alamat" required>
        </p>
        <p>
            <label for="no_hp">No. HP</label><br>
            <input type="text" id="no_hp" name="no_hp" required>
        </p>
        <p>
            <button type="submit">Simpan</button>
        </p>
    </form>
</section>

<?php include '../includes/footer.php'; ?>