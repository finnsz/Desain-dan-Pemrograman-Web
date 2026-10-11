<?php
$page_title = "Tambah Anggota";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section class="form-card">
            <div class="form-head">
                <div>
                    <h2>Tambah Anggota</h2>
                    <p>Daftarkan anggota baru untuk layanan perpustakaan.</p>
                </div>
            </div>

            <div class="form-body">
                <?php if ($flash): ?>
                    <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
                <?php endif; ?>

                <form id="form-tambah" method="post" action="proses_tambah.php">
                    <div class="form-grid">
                        <p class="full">
                            <label for="nama">Nama <span class="req">*</span></label>
                            <input type="text" id="nama" name="nama" placeholder="Nama lengkap" required>
                        </p>
                        <p>
                            <label for="no_anggota">No. Anggota <span class="req">*</span></label>
                            <input type="text" id="no_anggota" name="no_anggota" placeholder="AG-001" required>
                        </p>
                        <p>
                            <label for="no_hp">No. HP</label>
                            <input type="text" id="no_hp" name="no_hp" placeholder="0812-...">
                        </p>
                        <p class="full">
                            <label for="alamat">Alamat</label>
                            <input type="text" id="alamat" name="alamat" placeholder="Alamat lengkap anggota">
                        </p>
                        <p class="full form-actions">
                            <button type="submit" class="btn-simpan">Simpan</button>
                            <a href="list.php" class="btn-batal">&#8592; Batal</a>
                            <small class="form-note">* wajib diisi</small>
                        </p>
                    </div>
                </form>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
