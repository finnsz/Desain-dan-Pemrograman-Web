-- 1. Buat Tabel Kamar
CREATE TABLE IF NOT EXISTS kamar (
    id SERIAL PRIMARY KEY,
    nomor_kamar VARCHAR(10) NOT NULL UNIQUE,
    tipe VARCHAR(50) NOT NULL,
    harga DECIMAL(12, 2) NOT NULL,
    status VARCHAR(20) DEFAULT 'KOSONG'
);

-- 2. Buat Tabel Penghuni
CREATE TABLE IF NOT EXISTS penghuni (
    id SERIAL PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    no_hp VARCHAR(15) NOT NULL,
    kamar_id INT REFERENCES kamar(id) ON DELETE SET NULL,
    tgl_masuk DATE NOT NULL DEFAULT CURRENT_DATE
);

-- 3. Data Awal Dummy (Opsional)
INSERT INTO kamar (nomor_kamar, tipe, harga, status) VALUES
('K01', 'AC + KM Dalam', 1200000, 'TERISI'),
('K02', 'Non-AC', 800000, 'KOSONG');

INSERT INTO penghuni (nama_lengkap, no_hp, kamar_id, tgl_masuk) VALUES
('Siti Aminah', '081234567890', 1, '2026-01-10');