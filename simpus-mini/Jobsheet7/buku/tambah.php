<?php
$page_title = "Tambah Buku";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section class="form-card">
            <div class="form-head">
                <div>
                    <h2>Tambah Buku</h2>
                    <p>Lengkapi data buku baru untuk katalog perpustakaan.</p>
                </div>
            </div>

            <div class="form-body">
                <?php if ($flash): ?>
                    <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
                <?php endif; ?>

                <form id="form-tambah" method="post" action="proses_tambah.php">
                    <div class="form-grid">
                        <p class="full">
                            <label for="judul">Judul <span class="req">*</span></label>
                            <input type="text" id="judul" name="judul" placeholder="Contoh: Laskar Pelangi" required>
                        </p>
                        <p>
                            <label for="pengarang">Pengarang <span class="req">*</span></label>
                            <input type="text" id="pengarang" name="pengarang" placeholder="Nama pengarang" required>
                        </p>
                        <p>
                            <label for="tahun">Tahun Terbit <span class="req">*</span></label>
                            <input type="number" id="tahun" name="tahun" min="1900" max="2026" placeholder="2024" required>
                        </p>
                        <p>
                            <label for="isbn">ISBN</label>
                            <input type="text" id="isbn" name="isbn" placeholder="978-602-...">
                        </p>
                        <p>
                            <label for="stok">Stok <span class="req">*</span></label>
                            <input type="number" id="stok" name="stok" min="0" placeholder="5" required>
                        </p>
                        <p>
                            <label for="kategori">Kategori</label>
                            <select id="kategori" name="kategori">
                                <option value="fiksi">Fiksi</option>
                                <option value="non-fiksi">Non-Fiksi</option>
                                <option value="referensi">Referensi</option>
                            </select>
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
