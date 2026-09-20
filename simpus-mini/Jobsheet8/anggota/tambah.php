<?php
$base = '../';
$title = 'SIMPUS-Mini | Tambah Anggota';
include __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Tambah Anggota</h2>
    <form action="proses_tambah.php" method="POST" id="form-tambah">
        <p>
            <label for="no_anggota">No. Anggota</label><br>
            <input type="text" id="no_anggota" name="no_anggota" placeholder="Contoh: A003" required>
        </p>
        <p>
            <label for="nama">Nama Lengkap</label><br>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
        </p>
        <p>
            <label for="alamat">Alamat</label><br>
            <input type="text" id="alamat" name="alamat" placeholder="Kota / Alamat tinggal" required>
        </p>
        <p>
            <label for="no_hp">No. Handphone / WhatsApp</label><br>
            <input type="text" id="no_hp" name="no_hp" placeholder="08xxxxxxxxxx" required>
        </p>
        <p style="margin-top: 1.5rem;">
            <button type="submit">Simpan Anggota</button>
            <a href="list.php" style="margin-left: 12px; color: var(--text-muted);">Batal</a>
        </p>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>