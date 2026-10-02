# Security Checklist — Jobsheet 11

Audit keamanan menyeluruh terhadap aplikasi SIMKOS (Sistem Manajemen Kost) yang mencakup 5 kerentanan utama.

## Tabel Audit

| # | Kerentanan | Ditemukan di | Sebelum | Sesudah (perbaikan) |
|---|---|---|---|---|
| 1 | **SQL Injection** | Semua query di `kamar/`, `penghuni/`, `auth/` | Diaudit ulang, sudah aman sejak implementasi prepared statement — semua query menggunakan PDO dengan placeholder (`:parameter` atau `?`), tidak ada satupun yang menyisipkan `$_POST`/`$_GET` langsung ke string SQL. | Tidak ada perubahan kode. **Bukti uji:** Login dengan username `' OR '1'='1` → tetap muncul "Username atau password salah", membuktikan prepared statement bekerja mencegah SQL injection. |
| 2 | **XSS (Cross-Site Scripting)** | Output di `kamar/list.php`, `kamar/edit.php`, `penghuni/list.php`, `penghuni/edit.php`, `index.php`, `includes/header.php` (navbar dan flash message) | Data dari database/`$_GET`/`$_SESSION` dicetak langsung dengan `<?= htmlspecialchars(...) ?>` atau `<?= ... ?>` tanpa escaping konsisten. Contoh: `<?= $kamar['nomor_kamar'] ?>` di `kamar/list.php`. | Semua output data teks dibungkus fungsi `e()` (wrapper untuk `htmlspecialchars()` dengan `ENT_QUOTES` dan `UTF-8`). Contoh: `<?php echo e($kamar['nomor_kamar']); ?>`. Kolom INTEGER (harga, id) tidak dibungkus karena tidak mungkin berisi HTML. **Bukti uji:** Tambah kamar dengan nomor `<script>alert(1)</script>` → tampil sebagai teks di Daftar Kamar, tidak ada pop-up alert. |
| 3 | **CSRF (Cross-Site Request Forgery)** | Semua form POST di `kamar/tambah.php`, `kamar/edit.php`, `kamar/list.php` (form hapus), `penghuni/tambah.php`, `penghuni/edit.php`, `penghuni/list.php` (form hapus), `auth/login.php`, `auth/register.php` | Form POST tidak memiliki token verifikasi, rentan terhadap serangan CSRF dari situs lain. | Ditambahkan `includes/csrf.php` dengan 3 fungsi: `csrf_token()` (generate token per-sesi), `csrf_field()` (HTML hidden input), `csrf_verify()` (verifikasi di proses). Setiap form POST menambahkan `<?php echo csrf_field(); ?>`. Setiap file proses menambahkan `csrf_verify();` setelah require auth/csrf/koneksi. **Bukti uji:** Login lewat browser, lalu `curl -X POST http://localhost:8000/kamar/proses_tambah.php -d "nomor_kamar=x"` tanpa csrf_token → HTTP 403 "Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa." |
| 4 | **Validasi & Sanitasi Input** | Form tambah/edit di `kamar/`, `penghuni/`, `auth/` | Diaudit ulang, sudah ada validasi server-side (`trim()`, pengecekan empty, `is_numeric()` untuk beberapa field). Namun hidden input `id` di form edit tidak eksplisit di-cast. | Ditambahkan type casting eksplisit `(int)` di hidden input: `<input type="hidden" name="id" value="<?php echo (int) $kamar['id']; ?>">` di `kamar/edit.php` dan `penghuni/edit.php`. Validasi yang sudah ada tetap dipertahankan. |
| 5 | **Session Fixation** | `auth/proses_login.php` | Session ID tidak berubah setelah login berhasil, memungkinkan penyerang memaksa korban memakai session ID yang sudah diketahui penyerang sebelum login. | Ditambahkan `session_regenerate_id(true);` tepat setelah `password_verify()` berhasil dan **sebelum** mengisi `$_SESSION['user_id']`. Komentar ditambahkan: "Regenerasi session ID setelah login berhasil untuk mencegah session fixation." **Bukti:** Session ID berubah setiap kali login berhasil (bisa dilihat dari cookie `PHPSESSID` di browser developer tools). |

## Catatan Implementasi

### Urutan Guard dan CSRF
Guard `includes/auth.php` selalu dijalankan **sebelum** `includes/csrf.php` di semua halaman proses (kamar/penghuni). Urutan wajib:
```php
require __DIR__ . '/../includes/auth.php';      // Guard login
require __DIR__ . '/../includes/csrf.php';       // CSRF functions
require_once __DIR__ . '/../config/database.php';
csrf_verify();
```

Ini memastikan pengguna yang belum login tidak bisa memicu pengecekan CSRF sama sekali (langsung di-redirect ke login oleh `auth.php`).

### File Baru
- `includes/helpers.php` — fungsi `e()` untuk XSS protection
- `includes/csrf.php` — fungsi `csrf_token()`, `csrf_field()`, `csrf_verify()`
- `docs/security-checklist.md` — dokumen ini

### File Diubah
- `includes/header.php` — require helpers.php & csrf.php, wrap output `$_SESSION['nama']` dan flash message dengan `e()`
- `kamar/tambah.php`, `kamar/edit.php` — tambah `csrf_field()`, wrap value dengan `e()`, type cast `(int)` untuk id
- `kamar/list.php` — tambah `csrf_field()` di form hapus, wrap output dengan `e()`
- `kamar/proses_tambah.php`, `kamar/proses_edit.php`, `kamar/hapus.php` — tambah `csrf_verify()`
- `penghuni/tambah.php`, `penghuni/edit.php` — sama dengan kamar
- `penghuni/list.php` — sama dengan kamar
- `penghuni/proses_tambah.php`, `penghuni/proses_edit.php`, `penghuni/hapus.php` — tambah `csrf_verify()`
- `auth/login.php`, `auth/register.php` — tambah `csrf_field()`
- `auth/proses_login.php` — tambah `csrf_verify()` dan `session_regenerate_id(true)`
- `auth/proses_register.php` — tambah `csrf_verify()`
- `index.php` — wrap output `$_SESSION['nama']` dan data penghuni dengan `e()`

## Cara Menguji

### 1. CSRF Protection
```bash
# Login lewat browser dulu, lalu:
curl -X POST http://localhost:8000/kamar/proses_tambah.php -d "nomor_kamar=HACK"
# Expected: HTTP 403 "Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa."
```

### 2. XSS Protection
1. Login dan tambah kamar dengan nomor: `<script>alert('XSS')</script>`
2. Buka Daftar Kamar
3. Expected: Teks `<script>alert('XSS')</script>` tampil apa adanya, **tidak** ada pop-up alert

### 3. Urutan Guard
```bash
# POST ke proses tanpa login:
curl -X POST http://localhost:8000/kamar/proses_tambah.php -d "nomor_kamar=x&csrf_token=fake"
# Expected: Redirect ke login (HTTP 302) atau pesan redirect, BUKAN pesan CSRF
```

### 4. SQL Injection
1. Buka halaman login
2. Username: `' OR '1'='1`
3. Password: apa saja
4. Expected: "Username atau password salah", BUKAN login berhasil

## Kesimpulan

Aplikasi SIMKOS telah melalui audit keamanan lengkap dan perbaikan untuk 5 kerentanan utama. Dua kerentanan (SQL Injection, Validasi Input) sudah aman sejak implementasi sebelumnya dan hanya diaudit ulang. Tiga kerentanan lainnya (XSS, CSRF, Session Fixation) telah diperbaiki dengan penambahan helper function dan proteksi di setiap titik input/output yang relevan.
