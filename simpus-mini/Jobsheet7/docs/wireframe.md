Userflow peminjaman buku
[Petugas Login] -> [Dashboard] -> [Pilih menu "Peminjaman Baru"]
        -> [Pilih Anggota] -> [Pilih Buku (stok > 0)]
        -> [Simpan] -> [Stok buku berkurang 1] -> [Kembali ke Dashboard]

Userflow pengembalian buku
[Petugas Login] -> [Dashboard] -> [Pilih menu "Peminjaman Baru"]
        -> [Pilih Anggota] -> [Pilih Buku (stok > 0)]
        -> [Simpan] -> [Stok buku berkurang 1] -> [Kembali ke Dashboard]

Wireframe halaman login
+--------------------------------------+
|              SIMPUS-Mini             |
|--------------------------------------|
|                                      |
|        [ Login Petugas ]            |
|                                      |
|   Username : [______________]       |
|   Password : [______________]       |
|                                      |
|          [   Masuk   ]              |
|                                      |
|   Belum punya akun? Daftar di sini  |
+--------------------------------------+

Wireframe dashboard petugas
+-----------------------------------------------------+
| SIMPUS-Mini      Beranda | Buku | Anggota | Peminjaman | (Nama Petugas) Logout |
|-------------------------------------------------------|
|  [Total Buku]   [Total Anggota]   [Sedang Dipinjam]    |
|                                                         |
|  Aksi Cepat:                                           |
|  [ + Peminjaman Baru ]   [ + Pengembalian ]            |
|                                                         |
|  Transaksi Terbaru                                     |
|  --------------------------------------------------    |
|  Anggota | Buku | Tgl Pinjam | Status                  |
+-----------------------------------------------------+

Wireframe form peminjaman
+--------------------------------------+
|  Form Peminjaman Buku                |
|--------------------------------------|
|  Anggota : [ dropdown pilih anggota ]|
|  Buku    : [ dropdown, hanya stok>0 ]|
|  Tanggal Pinjam : [ auto: hari ini ] |
|                                      |
|          [  Simpan Peminjaman  ]    |
+--------------------------------------+

Wireframe form pengembalian
+--------------------------------------+
|  Pengembalian Buku                   |
|--------------------------------------|
|  Cari transaksi aktif:               |
|  [ nama anggota / judul buku ______ ]|
|                                      |
|  Anggota | Buku | Tgl Pinjam | [Kembalikan] |
+--------------------------------------+


Wireframe riwayat peminjaman per anggota
+--------------------------------------+
|  Riwayat Peminjaman — Siti Aminah    |
|--------------------------------------|
|  Buku            | Pinjam   | Kembali | Status      |
|  Laskar Pelangi   | 01/07    | 10/07   | Selesai     |
|  Bumi Manusia      | 15/07    | -       | Dipinjam    |
+--------------------------------------+

Wireframe registrasi anggota baru
+-----------------------------------------------------------------------+
|  Registrasi anggota baru                                              |
+-----------------------------------------------------------------------+
|                                                                       |
|  Nama Lengkap                                                         |
|  [________________]                                                   |
|                                                                       |
|  Nomor Identitas (NIM)                                                |
|  [________________]                                                   |
|                                                                       |
|  Email                                                                |
|  [________________]                                                   |
|                                                                       |
|  Nomor Telepon / WhatsApp                                             |
|  [________________]                                                   |
|                                                                       |
|  Alamat Tinggal                                                       |
|  [________________]                                                   |
|                                                                       |
|  [ ] Saya menyetujui seluruh tata tertib & denda perpustakaan         |
|                                                                       |
|  [ Daftar Sekarang ]   [ Batal ]                                      |
|                                                                       |
+-----------------------------------------------------------------------+

Userflow Petugas mencari anggota yang tunggakannya sudah lewat jatuh tempo
[Petugas Login] -> [Dashboard] -> [Pilih menu "Daftar Peminjaman"]
-> [Pilih Filter "Lewat Jatuh Tempo"]
-> [Sistem Tampilkan Daftar Anggota & Buku yang Menunggak]
-> [Pilih salah satu Anggota] -> [Lihat Rincian Denda & Hari Terlambat]
-> [Pilih Aksi "Kirim Peringatan" atau "Proses Pengembalian"]
-> [Kembali ke Dashboard]