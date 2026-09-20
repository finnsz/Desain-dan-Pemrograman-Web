<?php
$base = './';
$title = 'SIMPUS-Mini | Beranda';
include 'includes/header.php';

// Inisialisasi awal data di session jika belum ada
if (!isset($_SESSION['buku'])) {
    $_SESSION['buku'] = [
        ["judul" => "Laskar Pelangi", "pengarang" => "Andrea Hirata", "tahun" => 2005, "isbn" => "978-979", "stok" => 4, "kategori" => "Fiksi"],
        ["judul" => "Filosofi Teras", "pengarang" => "Henry Manampiring", "tahun" => 2018, "isbn" => "978-602", "stok" => 5, "kategori" => "Non-Fiksi"]
    ];
}

if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [
        ["no_anggota" => "A001", "nama" => "Siti Aminah", "alamat" => "Malang", "no_hp" => "0812xxxx"],
        ["no_anggota" => "A002", "nama" => "Budi Santoso", "alamat" => "Batu", "no_hp" => "0813xxxx"]
    ];
}
?>

<section>
    <h2>Selamat Datang di Sistem Perpustakaan Mini</h2>
    <p>Aplikasi sederhana untuk mengelola data buku dan anggota perpustakaan.</p>
</section>

<section>
    <h2>Ringkasan</h2>
    <article>
        <h3>Total Buku</h3>
        <p><?= count($_SESSION['buku']) ?></p>
    </article>
    <article>
        <h3>Total Anggota</h3>
        <p><?= count($_SESSION['anggota']) ?></p>
    </article>
</section>

<?php include 'includes/footer.php'; ?>