# README — Jobsheet 11: Keamanan Web Dasar

Jobsheet ini melanjutkan dari Jobsheet 10 (Autentikasi & Manajemen Sesi) dan menutup celah keamanan yang sudah diidentifikasi sejak jobsheet sebelumnya.

## Ringkasan 5 Kerentanan yang Diaudit

| # | Kerentanan | Status |
|---|---|---|
| 1 | SQL Injection | ✅ Aman (diaudit ulang, prepared statement sejak J08) |
| 2 | XSS | ✅ Diperbaiki (fungsi `e()` diterapkan di semua output) |
| 3 | CSRF | ✅ Diperbaiki (token per-sesi di semua form POST) |
| 4 | Validasi & Sanitasi Input | ✅ Aman (diaudit ulang, type casting ditambah) |
| 5 | Session Fixation | ✅ Diperbaiki (session_regenerate_id setelah login) |

## File Baru

- `includes/helpers.php` — fungsi `e()` untuk escape HTML (XSS protection)
- `includes/csrf.php` — fungsi CSRF token generation & verification
- `docs/security-checklist.md` — laporan audit terstruktur dengan bukti pengujian

## File yang Diubah

### Includes
- `includes/header.php` — require helpers & csrf, wrap output dengan `e()`

### Authentication
- `auth/login.php`, `auth/register.php` — tambah `csrf_field()`
- `auth/proses_login.php` — tambah `csrf_verify()` & `session_regenerate_id(true)`
- `auth/proses_register.php` — tambah `csrf_verify()`

### Kamar
- `kamar/tambah.php`, `kamar/edit.php` — tambah `csrf_field()`, wrap value dengan `e()`, type cast `id`
- `kamar/list.php` — tambah `csrf_field()` di form hapus, wrap output dengan `e()`
- `kamar/proses_tambah.php`, `kamar/proses_edit.php`, `kamar/hapus.php` — tambah `csrf_verify()`

### Penghuni
- `penghuni/tambah.php`, `penghuni/edit.php` — sama dengan kamar
- `penghuni/list.php` — sama dengan kamar
- `penghuni/proses_tambah.php`, `penghuni/proses_edit.php`, `penghuni/hapus.php` — tambah `csrf_verify()`

### Lainnya
- `index.php` — wrap output dengan `e()`

## Cara Menguji

### 1. CSRF Protection
```bash
# Login lewat browser dulu (http://localhost:8000/auth/login.php)
# Lalu di terminal jalankan:
curl -X POST http://localhost:8000/kamar/proses_tambah.php -d "nomor_kamar=TEST"
```
**Expected:** HTTP 403 dengan pesan "Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa."

### 2. XSS Protection
1. Login ke aplikasi
2. Buka "Tambah Kamar" (`kamar/tambah.php`)
3. Isi Nomor Kamar dengan: `<script>alert('XSS')</script>`
4. Isi Tipe Kamar dan Harga dengan nilai normal
5. Klik Simpan
6. Buka Daftar Kamar

**Expected:** Teks `<script>alert('XSS')</script>` tampil apa adanya di kolom Nomor Kamar, **tidak** ada pop-up alert muncul.

### 3. Urutan Guard (Login Guard Sebelum CSRF)
```bash
# POST ke proses_tambah tanpa login sama sekali:
curl -X POST http://localhost:8000/kamar/proses_tambah.php -d "nomor_kamar=TEST&csrf_token=fake"
```
**Expected:** Browser diarahkan ke login (HTTP 302 redirect), bukan pesan CSRF.

### 4. SQL Injection Protection
1. Buka halaman login (`auth/login.php`)
2. Username: `' OR '1'='1`
3. Password: apa saja
4. Klik Masuk

**Expected:** Pesan "Username atau password salah." (TIDAK berhasil login, membuktikan prepared statement aman)

## Struktur Folder

```
jobsheet-11/
├── includes/
│   ├── helpers.php           ✨ BARU — fungsi e()
│   ├── csrf.php              ✨ BARU — token CSRF
│   ├── header.php            📝 DIUBAH
│   ├── auth.php
│   ├── session.php
│   └── ...
├── auth/
│   ├── login.php             📝 DIUBAH
│   ├── register.php          📝 DIUBAH
│   ├── proses_login.php      📝 DIUBAH
│   ├── proses_register.php   📝 DIUBAH
│   └── logout.php
├── kamar/
│   ├── list.php              📝 DIUBAH
│   ├── tambah.php            📝 DIUBAH
│   ├── edit.php              📝 DIUBAH
│   ├── proses_tambah.php     📝 DIUBAH
│   ├── proses_edit.php       📝 DIUBAH
│   └── hapus.php             📝 DIUBAH
├── penghuni/
│   ├── list.php              📝 DIUBAH
│   ├── tambah.php            📝 DIUBAH
│   ├── edit.php              📝 DIUBAH
│   ├── proses_tambah.php     📝 DIUBAH
│   ├── proses_edit.php       📝 DIUBAH
│   └── hapus.php             📝 DIUBAH
├── docs/
│   ├── security-checklist.md ✨ BARU — audit lengkap
│   └── wireframe.md
├── index.php                 📝 DIUBAH
├── README.md                 📝 DIUBAH (dokumen ini)
└── ...
```

## Konsep Inti

### `e()` — Escape untuk XSS
Setiap output data dari database/`$_GET`/`$_SESSION` dibungkus `e()`:
```php
<?php echo e($kamar['nomor_kamar']); ?>  // Aman dari XSS
```

Fungsi ini menggunakan `htmlspecialchars()` dengan flag `ENT_QUOTES` dan encoding `UTF-8` untuk mengubah karakter berbahaya (`<`, `>`, `&`, `"`, `'`) menjadi HTML entity.

### CSRF Token — Verifikasi Sesi
Setiap form `POST` menyertakan token tersembunyi yang unik per-sesi:
```php
<?php echo csrf_field(); ?>
```

Setiap proses memverifikasi token sebelum mengubah data:
```php
csrf_verify();  // Hentikan jika token tidak valid
```

### Session Regeneration — Cegah Session Fixation
Setelah login berhasil, ID sesi diganti dengan ID baru yang acak:
```php
session_regenerate_id(true);  // Setelah password_verify() berhasil
```

## Catatan Penting

1. **Guard sebelum CSRF:** Di semua file proses, `auth.php` **harus** dijalankan sebelum `csrf.php`. Urutan: auth → csrf → koneksi → csrf_verify().

2. **Form GET tidak perlu token:** Form pencarian dengan `method="GET"` (tidak mengubah data) tidak perlu CSRF token — hanya form `POST` yang mengubah data.

3. **Kolom INTEGER tidak perlu `e()`:** Kolom seperti `harga` (tipe integer) tidak bisa berisi HTML, jadi tidak perlu dibungkus `e()`. Tapi tidak apa-apa jika dibungkus — tidak akan merusak.

4. **Prepared Statement Tetap:** Semua query masih menggunakan prepared statement (`:parameter` atau `?`) — jangan kembali ke string concatenation.

## Referensi Dokumentasi

- [Dokumentasi Jobsheet 11 — Lengkap](Dokumentasi/README.md)
- [XSS & Fungsi e()](Dokumentasi/02-xss-dan-fungsi-e.md)
- [CSRF & Token](Dokumentasi/03-csrf-dan-token.md)
- [Session Fixation](Dokumentasi/04-session-fixation.md)
- [Security Checklist](docs/security-checklist.md)

## Verifikasi Kode

Setelah implementasi selesai, jalankan:

```bash
# 1. Syntax check semua file PHP
php -l includes/helpers.php
php -l includes/csrf.php
php -l kamar/proses_tambah.php
php -l penghuni/proses_tambah.php
# ... dan file-file lainnya

# 2. Cari output yang belum dibungkus e()
grep -rn "<?php echo" kamar penghuni index.php | grep -v " e("

# 3. Pastikan semua form POST punya csrf_field()
grep -rn "method=\"post\"" -i . | grep "form"

# 4. Pastikan semua proses punya csrf_verify()
grep -rn "csrf_verify" kamar penghuni auth/
```

---

**Jobsheet ini menyelesaikan rangkaian keamanan dasar web.** Kelima kerentanan yang diaudit merepresentasikan lapisan pertama pertahanan di aplikasi web modern. Praktik-praktik ini harus menjadi kebiasaan otomatis dalam setiap proyek web yang melibatkan input/output pengguna.
