    -- 1. Buat Tabel Buku
CREATE TABLE IF NOT EXISTS buku (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    pengarang VARCHAR(100) NOT NULL,
    tahun INT NOT NULL,
    isbn VARCHAR(20),
    stok INT DEFAULT 0,
    kategori VARCHAR(50)
);

-- 2. Buat Tabel Anggota
CREATE TABLE IF NOT EXISTS anggota (
    id SERIAL PRIMARY KEY,
    no_anggota VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT,
    no_hp VARCHAR(15)
);

-- 3. Insert Data Awal (Opsional)
INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori) VALUES
('Laskar Pelangi', 'Andrea Hirata', 2005, '978-979', 4, 'Fiksi'),
('Filosofi Teras', 'Henry Manampiring', 2018, '978-602', 5, 'Non-Fiksi');

INSERT INTO anggota (no_anggota, nama, alamat, no_hp) VALUES
('A001', 'Siti Aminah', 'Malang', '081234567890'),
('A002', 'Budi Santoso', 'Batu', '081398765432');