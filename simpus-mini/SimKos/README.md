# README — Jobsheet 12: Transaction, JOIN, dan FOR UPDATE

Jobsheet ini melanjutkan Jobsheet 11 (Keamanan Web Dasar) dan menerapkan pola-pola database lanjut:
- **Transaction** — `beginTransaction`, `commit`, `rollBack`
- **JOIN** — menggabungkan data multi-tabel secara explicit
- **FOR UPDATE** — mencegah race condition pada duplikat check
- **Defense in Depth** — berlapis proteksi (validation + constraint + prepared statement + transaction)

## Konsep Inti

### 1. Transaction untuk Data Consistency

Operasi catat pembayaran melibatkan:
1. Check duplikat periode
2. Upload file bukti (optional)
3. INSERT ke database

Transaction memastikan **semua berhasil atau semua gagal** — tidak ada state setengah-jadi.

```php
try {
    $pdo->beginTransaction();
    
    // Validasi + Insert
    $pdo->commit();  // Confirm permanen
} catch (Exception $e) {
    $pdo->rollBack();  // Batalkan semua
    // Cleanup file upload
}
```

### 2. FOR UPDATE Mencegah Race Condition

**Skenario:** 2 petugas generate tagihan periode sama secara bersamaan.

**Tanpa FOR UPDATE:** Keduanya bisa lolos duplikat check, hasilnya constraint violation.

**Dengan FOR UPDATE:**
```php
$check = $pdo->prepare("
    SELECT id FROM pembayaran 
    WHERE penghuni_id = :id AND periode_bulan = :bulan AND periode_tahun = :tahun
    FOR UPDATE  -- Kunci baris!
");
```

Proses kedua **menunggu** sampai proses pertama selesai, baru jalankan SELECT-nya — deteksi duplikat benar.

### 3. JOIN Explicit

Menggabungkan 3 tabel (`pembayaran`, `penghuni`, `kamar`) dalam 1 query:

```php
$query = "SELECT pb.id, p.nama_lengkap, k.nomor_kamar, pb.nominal, pb.status
          FROM pembayaran pb
          JOIN penghuni p ON pb.penghuni_id = p.id
          LEFT JOIN kamar k ON p.kamar_id = k.id
          WHERE ...";
```

**Alias tabel** (`pb`, `p`, `k`) membuat query ringkas. **LEFT JOIN** untuk kamar karena penghuni boleh tidak punya kamar (NULL-safe).

## File yang Diubah

## File yang Diubah

### Pembayaran (Transaction + FOR UPDATE)
- `pembayaran/proses_tambah.php` — Transaction dengan FOR UPDATE, file upload cleanup
- `pembayaran/proses_generate.php` — Bulk insert dengan FOR UPDATE per penghuni
- `pembayaran/list.php` — JOIN explicit (pembayaran + penghuni + kamar)

### Dokumentasi
- `Dokumentasi/07-transaction-join-for-update.md` — **BARU** — Penjelasan lengkap pola yang diterapkan
- `README.md` — Update dokumentasi ini

## Struktur Folder

```
jobsheet-12/
├── pembayaran/
│   ├── tambah.php
│   ├── proses_tambah.php         📝 DIUBAH — transaction + FOR UPDATE
│   ├── generate.php
│   ├── proses_generate.php       📝 DIUBAH — FOR UPDATE di bulk insert
│   ├── list.php                  📝 DIUBAH — JOIN explicit
│   ├── edit.php, proses_edit.php
│   └── kwitansi.php
├── Dokumentasi/
│   └── 07-transaction-join-for-update.md  ✨ BARU
├── database/database.sql
├── README.md                     📝 DIUBAH
└── ...
```

## Cara Menguji

### 1. Race Condition Protection (FOR UPDATE)

**Setup:** Buka 2 tab browser, keduanya login.

**Test:**
1. **Tab A:** Buka `/pembayaran/tambah.php`, isi form (Penghuni: Budi, Bulan: 10, Tahun: 2024)
2. **Tab B:** Buka `/pembayaran/tambah.php`, isi **identik** (Penghuni: Budi, Bulan: 10, Tahun: 2024)
3. **Tab A:** Klik "Simpan"
4. **Tab B:** Klik "Simpan" dalam 1-2 detik (sebelum page refresh)

**Expected:**
- Tab A: ✅ "Pembayaran berhasil dicatat!"
- Tab B: ❌ "Tagihan untuk periode ini sudah ada. Gunakan Edit jika ingin mengubah."

**Bukti:** FOR UPDATE mengunci baris, Tab B menunggu Tab A selesai, deteksi duplikat benar.

### 2. Transaction Rollback (File Upload Cleanup)

**Test:**
1. Login, buka `/pembayaran/tambah.php`
2. Isi form dengan **periode yang sudah ada** (untuk trigger error)
3. Upload file bukti (PDF/JPG)
4. Klik Simpan
5. Check folder `assets/bukti/`

**Expected:**
- Error: "Tagihan untuk periode ini sudah ada"
- Folder `assets/bukti/` **tidak** ada file baru (di-cleanup oleh rollback)

### 3. JOIN Query Verification

**Test:**
1. Buka `/pembayaran/list.php`
2. Check source code — query SELECT harus ada `FROM pembayaran pb JOIN penghuni p ON ... LEFT JOIN kamar k ON ...`
3. Data nama penghuni dan nomor kamar tampil benar

**Expected:** Data lengkap dari 3 tabel, tidak pakai view (`v_pembayaran_detail`).

## Konsep yang Perlu Diingat

1. **Transaction = All-or-Nothing** — `beginTransaction` + `commit`/`rollBack`
2. **FOR UPDATE = Lock Baris** — mencegah concurrent modification
3. **JOIN = Multi-Table Merge** — `pb JOIN p ON pb.penghuni_id = p.id`
4. **Defense in Depth** — validation + constraint + prepared statement + transaction
5. **File Upload Cleanup** — hapus file kalau transaction gagal

## Referensi Dokumentasi

- [Transaction, JOIN, dan FOR UPDATE](Dokumentasi/07-transaction-join-for-update.md) — **Baca ini dulu!**
- [Keamanan Web (Jobsheet 11)](../Jobsheet11/README.md)
- Database Schema: `database/database.sql`

---

**Jobsheet 12 menggabungkan semua pola dari Jobsheet 1-11:** HTML/CSS, PHP, PostgreSQL, session, auth, keamanan, dan sekarang transaction + concurrency control. Ini foundation untuk aplikasi web production-grade.
