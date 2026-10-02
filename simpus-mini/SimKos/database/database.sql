-- SimKos Database: Complete Schema + Sample Data
-- Consolidated from all database files
-- Run: psql -U postgres -d simkos -f database.sql

-- ========================================
-- SEQUENCES
-- ========================================
DROP SEQUENCE IF EXISTS kamar_id_seq CASCADE;
DROP SEQUENCE IF EXISTS penghuni_id_seq CASCADE;
DROP SEQUENCE IF EXISTS users_id_seq CASCADE;
DROP SEQUENCE IF EXISTS remember_tokens_id_seq CASCADE;
DROP SEQUENCE IF EXISTS pembayaran_id_seq CASCADE;
DROP SEQUENCE IF EXISTS pengeluaran_id_seq CASCADE;

CREATE SEQUENCE kamar_id_seq START 1;
CREATE SEQUENCE penghuni_id_seq START 1;
CREATE SEQUENCE users_id_seq START 1;
CREATE SEQUENCE remember_tokens_id_seq START 1;
CREATE SEQUENCE pembayaran_id_seq START 1;
CREATE SEQUENCE pengeluaran_id_seq START 1;

-- ========================================
-- TABLES
-- ========================================

-- Table: kamar
DROP TABLE IF EXISTS kamar CASCADE;
CREATE TABLE kamar (
  id integer NOT NULL DEFAULT nextval('kamar_id_seq'::regclass),
  nomor_kamar character varying NOT NULL UNIQUE,
  tipe character varying NOT NULL,
  harga numeric NOT NULL,
  status character varying DEFAULT 'KOSONG'::character varying,
  CONSTRAINT kamar_pkey PRIMARY KEY (id)
);

-- Table: penghuni
DROP TABLE IF EXISTS penghuni CASCADE;
CREATE TABLE penghuni (
  id integer NOT NULL DEFAULT nextval('penghuni_id_seq'::regclass),
  nama_lengkap character varying NOT NULL,
  no_hp character varying NOT NULL,
  kamar_id integer,
  tgl_masuk date NOT NULL DEFAULT CURRENT_DATE,
  CONSTRAINT penghuni_pkey PRIMARY KEY (id),
  CONSTRAINT penghuni_kamar_id_fkey FOREIGN KEY (kamar_id) REFERENCES kamar(id)
);

-- Table: users
DROP TABLE IF EXISTS users CASCADE;
CREATE TABLE users (
  id integer NOT NULL DEFAULT nextval('users_id_seq'::regclass),
  nama character varying NOT NULL,
  username character varying NOT NULL UNIQUE,
  password character varying NOT NULL,
  role character varying NOT NULL DEFAULT 'petugas'::character varying,
  CONSTRAINT users_pkey PRIMARY KEY (id)
);

-- Table: app_sessions
DROP TABLE IF EXISTS app_sessions CASCADE;
CREATE TABLE app_sessions (
  id character varying NOT NULL,
  data text NOT NULL DEFAULT ''::text,
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT app_sessions_pkey PRIMARY KEY (id)
);

-- Table: remember_tokens
DROP TABLE IF EXISTS remember_tokens CASCADE;
CREATE TABLE remember_tokens (
  id integer NOT NULL DEFAULT nextval('remember_tokens_id_seq'::regclass),
  user_id integer NOT NULL,
  selector character varying NOT NULL UNIQUE,
  token_hash character varying NOT NULL,
  expires_at timestamp without time zone NOT NULL,
  CONSTRAINT remember_tokens_pkey PRIMARY KEY (id),
  CONSTRAINT remember_tokens_user_fkey FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table: pembayaran
DROP TABLE IF EXISTS pembayaran CASCADE;
CREATE TABLE pembayaran (
  id integer NOT NULL DEFAULT nextval('pembayaran_id_seq'::regclass),
  penghuni_id integer NOT NULL,
  periode_bulan integer NOT NULL,
  periode_tahun integer NOT NULL,
  nominal numeric NOT NULL,
  tgl_jatuh_tempo date NOT NULL,
  tgl_bayar date,
  status character varying NOT NULL DEFAULT 'BELUM BAYAR',
  denda numeric DEFAULT 0,
  total_bayar numeric GENERATED ALWAYS AS (nominal + COALESCE(denda, 0)) STORED,
  metode_bayar character varying,
  bukti_path character varying,
  keterangan text,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  updated_at timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT pembayaran_pkey PRIMARY KEY (id),
  CONSTRAINT pembayaran_penghuni_fkey FOREIGN KEY (penghuni_id) REFERENCES penghuni(id) ON DELETE CASCADE,
  CONSTRAINT pembayaran_unique_periode UNIQUE (penghuni_id, periode_bulan, periode_tahun)
);

-- Table: pengeluaran
DROP TABLE IF EXISTS pengeluaran CASCADE;
CREATE TABLE pengeluaran (
  id integer NOT NULL DEFAULT nextval('pengeluaran_id_seq'::regclass),
  kategori character varying NOT NULL,
  nominal numeric NOT NULL,
  tanggal date NOT NULL DEFAULT CURRENT_DATE,
  keterangan text,
  bukti_path character varying,
  created_by integer,
  created_at timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT pengeluaran_pkey PRIMARY KEY (id),
  CONSTRAINT pengeluaran_user_fkey FOREIGN KEY (created_by) REFERENCES users(id)
);

-- ========================================
-- INDEXES
-- ========================================
CREATE INDEX IF NOT EXISTS idx_pembayaran_periode ON pembayaran(periode_tahun, periode_bulan);
CREATE INDEX IF NOT EXISTS idx_pembayaran_status ON pembayaran(status);
CREATE INDEX IF NOT EXISTS idx_pembayaran_penghuni ON pembayaran(penghuni_id);
CREATE INDEX IF NOT EXISTS idx_pengeluaran_tanggal ON pengeluaran(tanggal);

-- ========================================
-- VIEWS
-- ========================================
DROP VIEW IF EXISTS v_pembayaran_detail CASCADE;
CREATE OR REPLACE VIEW v_pembayaran_detail AS
SELECT
  pb.id,
  pb.penghuni_id,
  p.nama_lengkap,
  p.no_hp,
  k.nomor_kamar,
  k.tipe,
  k.harga as harga_sewa,
  pb.periode_bulan,
  pb.periode_tahun,
  pb.nominal,
  pb.denda,
  pb.total_bayar,
  pb.tgl_jatuh_tempo,
  pb.tgl_bayar,
  pb.status,
  pb.metode_bayar,
  pb.bukti_path,
  pb.keterangan,
  CASE
    WHEN pb.tgl_bayar IS NULL AND CURRENT_DATE > pb.tgl_jatuh_tempo THEN true
    ELSE false
  END as is_terlambat,
  CASE
    WHEN pb.tgl_bayar IS NULL AND CURRENT_DATE > pb.tgl_jatuh_tempo
    THEN CURRENT_DATE - pb.tgl_jatuh_tempo
    ELSE 0
  END as hari_terlambat
FROM pembayaran pb
JOIN penghuni p ON pb.penghuni_id = p.id
LEFT JOIN kamar k ON p.kamar_id = k.id;

-- ========================================
-- DATA: ADMIN USER
-- ========================================
INSERT INTO users (nama, username, password, role) VALUES
('Administrator', 'admin', '$2y$10$WFgLVJdzTMVHAjV3N0P4o.Y3qS/2uZGgWxT9B6M6RbOG3K5qsqxYm', 'admin');

-- ========================================
-- DATA: 20 KAMAR
-- ========================================
INSERT INTO kamar (nomor_kamar, tipe, harga, status) VALUES
('101', 'Standard', 800000, 'TERISI'),
('102', 'Standard', 800000, 'TERISI'),
('103', 'Standard', 800000, 'TERISI'),
('104', 'Standard', 850000, 'KOSONG'),
('105', 'Standard', 850000, 'KOSONG'),
('201', 'Deluxe', 1200000, 'TERISI'),
('202', 'Deluxe', 1200000, 'TERISI'),
('203', 'Deluxe', 1250000, 'TERISI'),
('204', 'Deluxe', 1250000, 'KOSONG'),
('205', 'Deluxe', 1200000, 'KOSONG'),
('301', 'VIP', 1500000, 'TERISI'),
('302', 'VIP', 1500000, 'TERISI'),
('303', 'VIP', 1600000, 'TERISI'),
('304', 'VIP', 1600000, 'KOSONG'),
('305', 'VIP', 1500000, 'KOSONG'),
('401', 'Standard', 900000, 'TERISI'),
('402', 'Deluxe', 1300000, 'TERISI'),
('403', 'VIP', 1700000, 'KOSONG'),
('404', 'Deluxe', 1300000, 'KOSONG'),
('405', 'Standard', 900000, 'KOSONG');

-- ========================================
-- DATA: 11 PENGHUNI
-- ========================================
INSERT INTO penghuni (nama_lengkap, no_hp, kamar_id, tgl_masuk) VALUES
('Budi Santoso', '081234567801', 1, '2024-01-15'),
('Siti Nurhaliza', '081234567802', 2, '2024-02-01'),
('Ahmad Fauzi', '081234567803', 3, '2024-03-10'),
('Dewi Lestari', '081234567804', 6, '2024-01-20'),
('Rizky Ramadhan', '081234567805', 7, '2024-04-05'),
('Putri Ayu Wulandari', '081234567806', 8, '2024-02-15'),
('Andi Wijaya', '081234567807', 11, '2024-05-01'),
('Mega Pratiwi', '081234567808', 12, '2024-03-25'),
('Hendra Gunawan', '081234567809', 13, '2024-06-10'),
('Fitri Handayani', '081234567810', 16, '2024-04-20'),
('Agus Setiawan', '081234567811', 17, '2024-05-15');

-- ========================================
-- DATA: PEMBAYARAN (Multiple Periods)
-- ========================================

INSERT INTO pembayaran (penghuni_id, periode_bulan, periode_tahun, nominal, tgl_jatuh_tempo, tgl_bayar, status, metode_bayar, denda) VALUES
(1, 1, 2024, 800000, '2024-02-15', '2024-02-10', 'LUNAS', 'Transfer', 0),
(4, 1, 2024, 1200000, '2024-02-15', '2024-02-14', 'LUNAS', 'Cash', 0),
(1, 2, 2024, 800000, '2024-03-15', '2024-03-12', 'LUNAS', 'Transfer', 0),
(2, 2, 2024, 800000, '2024-03-15', '2024-03-10', 'LUNAS', 'E-Wallet', 0),
(4, 2, 2024, 1200000, '2024-03-15', '2024-03-18', 'LUNAS', 'Cash', 150000),
(6, 2, 2024, 1250000, '2024-03-15', '2024-03-14', 'LUNAS', 'Transfer', 0),
(1, 3, 2024, 800000, '2024-04-15', '2024-04-14', 'LUNAS', 'Transfer', 0),
(2, 3, 2024, 800000, '2024-04-15', '2024-04-10', 'LUNAS', 'Cash', 0),
(3, 3, 2024, 800000, '2024-04-15', '2024-04-12', 'LUNAS', 'Transfer', 0),
(4, 3, 2024, 1200000, '2024-04-15', '2024-04-14', 'LUNAS', 'E-Wallet', 0),
(6, 3, 2024, 1250000, '2024-04-15', '2024-04-13', 'LUNAS', 'Transfer', 0),
(8, 3, 2024, 1500000, '2024-04-15', '2024-04-20', 'LUNAS', 'Cash', 250000),
(1, 4, 2024, 800000, '2024-05-15', '2024-05-10', 'LUNAS', 'Transfer', 0),
(2, 4, 2024, 800000, '2024-05-15', '2024-05-12', 'LUNAS', 'E-Wallet', 0),
(3, 4, 2024, 800000, '2024-05-15', '2024-05-14', 'LUNAS', 'Cash', 0),
(4, 4, 2024, 1200000, '2024-05-15', '2024-05-13', 'LUNAS', 'Transfer', 0),
(5, 4, 2024, 1200000, '2024-05-15', '2024-05-11', 'LUNAS', 'Cash', 0),
(6, 4, 2024, 1250000, '2024-05-15', '2024-05-10', 'LUNAS', 'Transfer', 0),
(8, 4, 2024, 1500000, '2024-05-15', '2024-05-14', 'LUNAS', 'E-Wallet', 0),
(10, 4, 2024, 900000, '2024-05-15', '2024-05-25', 'LUNAS', 'Cash', 500000),
(1, 5, 2024, 800000, '2024-06-15', '2024-06-14', 'LUNAS', 'Transfer', 0),
(2, 5, 2024, 800000, '2024-06-15', '2024-06-10', 'LUNAS', 'Cash', 0),
(3, 5, 2024, 800000, '2024-06-15', '2024-06-12', 'LUNAS', 'E-Wallet', 0),
(4, 5, 2024, 1200000, '2024-06-15', '2024-06-14', 'LUNAS', 'Transfer', 0),
(5, 5, 2024, 1200000, '2024-06-15', '2024-06-13', 'LUNAS', 'Cash', 0),
(6, 5, 2024, 1250000, '2024-06-15', '2024-06-11', 'LUNAS', 'Transfer', 0),
(7, 5, 2024, 1500000, '2024-06-15', '2024-06-10', 'LUNAS', 'E-Wallet', 0),
(8, 5, 2024, 1500000, '2024-06-15', '2024-06-14', 'LUNAS', 'Cash', 0),
(10, 5, 2024, 900000, '2024-06-15', '2024-06-12', 'LUNAS', 'Transfer', 0),
(11, 5, 2024, 1300000, '2024-06-15', '2024-06-20', 'LUNAS', 'Cash', 250000),
(1, 6, 2024, 800000, '2024-07-15', '2024-07-10', 'LUNAS', 'Transfer', 0),
(2, 6, 2024, 800000, '2024-07-15', '2024-07-12', 'LUNAS', 'Cash', 0),
(3, 6, 2024, 800000, '2024-07-15', '2024-07-14', 'LUNAS', 'E-Wallet', 0),
(4, 6, 2024, 1200000, '2024-07-15', '2024-07-13', 'LUNAS', 'Transfer', 0),
(5, 6, 2024, 1200000, '2024-07-15', '2024-07-11', 'LUNAS', 'Cash', 0),
(6, 6, 2024, 1250000, '2024-07-15', '2024-07-10', 'LUNAS', 'Transfer', 0),
(7, 6, 2024, 1500000, '2024-07-15', '2024-07-14', 'LUNAS', 'E-Wallet', 0),
(8, 6, 2024, 1500000, '2024-07-15', '2024-07-12', 'LUNAS', 'Cash', 0),
(9, 6, 2024, 1600000, '2024-07-15', '2024-07-10', 'LUNAS', 'Transfer', 0),
(10, 6, 2024, 900000, '2024-07-15', '2024-07-13', 'LUNAS', 'Cash', 0),
(11, 6, 2024, 1300000, '2024-07-15', '2024-07-11', 'LUNAS', 'E-Wallet', 0),
(1, 7, 2024, 800000, '2024-08-15', '2024-08-10', 'LUNAS', 'Transfer', 0),
(2, 7, 2024, 800000, '2024-08-15', '2024-08-12', 'LUNAS', 'Cash', 0),
(3, 7, 2024, 800000, '2024-08-15', '2024-08-19', 'LUNAS', 'E-Wallet', 200000),
(4, 7, 2024, 1200000, '2024-08-15', '2024-08-14', 'LUNAS', 'Transfer', 0),
(5, 7, 2024, 1200000, '2024-08-15', '2024-08-11', 'LUNAS', 'Cash', 0),
(6, 7, 2024, 1250000, '2024-08-15', '2024-08-13', 'LUNAS', 'Transfer', 0),
(7, 7, 2024, 1500000, '2024-08-15', '2024-08-14', 'LUNAS', 'E-Wallet', 0),
(8, 7, 2024, 1500000, '2024-08-15', '2024-08-10', 'LUNAS', 'Cash', 0),
(9, 7, 2024, 1600000, '2024-08-15', '2024-08-12', 'LUNAS', 'Transfer', 0),
(10, 7, 2024, 900000, '2024-08-15', '2024-08-11', 'LUNAS', 'Cash', 0),
(11, 7, 2024, 1300000, '2024-08-15', '2024-08-13', 'LUNAS', 'E-Wallet', 0),
(1, 8, 2024, 800000, '2024-09-15', '2024-09-10', 'LUNAS', 'Transfer', 0),
(2, 8, 2024, 800000, '2024-09-15', '2024-09-12', 'LUNAS', 'Cash', 0),
(3, 8, 2024, 800000, '2024-09-15', '2024-09-14', 'LUNAS', 'E-Wallet', 0),
(4, 8, 2024, 1200000, '2024-09-15', '2024-09-13', 'LUNAS', 'Transfer', 0),
(5, 8, 2024, 1200000, '2024-09-15', '2024-09-11', 'LUNAS', 'Cash', 0),
(6, 8, 2024, 1250000, '2024-09-15', '2024-09-20', 'LUNAS', 'Transfer', 250000),
(7, 8, 2024, 1500000, '2024-09-15', '2024-09-14', 'LUNAS', 'E-Wallet', 0),
(8, 8, 2024, 1500000, '2024-09-15', '2024-09-10', 'LUNAS', 'Cash', 0),
(9, 8, 2024, 1600000, '2024-09-15', '2024-09-12', 'LUNAS', 'Transfer', 0),
(10, 8, 2024, 900000, '2024-09-15', '2024-09-11', 'LUNAS', 'Cash', 0),
(11, 8, 2024, 1300000, '2024-09-15', '2024-09-13', 'LUNAS', 'E-Wallet', 0),
(1, 9, 2024, 800000, '2024-10-15', '2024-10-10', 'LUNAS', 'Transfer', 0),
(2, 9, 2024, 800000, '2024-10-15', NULL, 'BELUM BAYAR', NULL, 0),
(3, 9, 2024, 800000, '2024-10-15', '2024-10-14', 'LUNAS', 'E-Wallet', 0),
(4, 9, 2024, 1200000, '2024-10-15', '2024-10-13', 'LUNAS', 'Transfer', 0),
(5, 9, 2024, 1200000, '2024-10-15', NULL, 'BELUM BAYAR', NULL, 0),
(6, 9, 2024, 1250000, '2024-10-15', NULL, 'BELUM BAYAR', NULL, 0),
(7, 9, 2024, 1500000, '2024-10-15', '2024-10-14', 'LUNAS', 'E-Wallet', 0),
(8, 9, 2024, 1500000, '2024-10-15', '2024-10-12', 'LUNAS', 'Cash', 0),
(9, 9, 2024, 1600000, '2024-10-15', '2024-10-11', 'LUNAS', 'Transfer', 0),
(10, 9, 2024, 900000, '2024-10-15', NULL, 'BELUM BAYAR', NULL, 0),
(11, 9, 2024, 1300000, '2024-10-15', NULL, 'BELUM BAYAR', NULL, 0),
(1, 10, 2024, 800000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0),
(2, 10, 2024, 800000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0),
(3, 10, 2024, 800000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0),
(4, 10, 2024, 1200000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0),
(5, 10, 2024, 1200000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0),
(6, 10, 2024, 1250000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0),
(7, 10, 2024, 1500000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0),
(8, 10, 2024, 1500000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0),
(9, 10, 2024, 1600000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0),
(10, 10, 2024, 900000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0),
(11, 10, 2024, 1300000, '2024-11-15', NULL, 'BELUM BAYAR', NULL, 0);

-- ========================================
-- DATA: PENGELUARAN (Operational Expenses)
-- ========================================
INSERT INTO pengeluaran (kategori, nominal, tanggal, keterangan, created_by) VALUES
('Listrik', 3500000, '2024-01-25', 'Tagihan listrik bulan Januari 2024', 1),
('Air', 800000, '2024-01-25', 'Tagihan air PDAM bulan Januari', 1),
('Internet', 500000, '2024-01-05', 'Langganan internet bulanan', 1),
('Listrik', 3200000, '2024-02-25', 'Tagihan listrik bulan Februari 2024', 1),
('Air', 750000, '2024-02-25', 'Tagihan air PDAM bulan Februari', 1),
('Internet', 500000, '2024-02-05', 'Langganan internet bulanan', 1),
('Perbaikan', 1500000, '2024-02-15', 'Perbaikan pipa bocor lantai 2', 1),
('Listrik', 3600000, '2024-03-25', 'Tagihan listrik bulan Maret 2024', 1),
('Air', 820000, '2024-03-25', 'Tagihan air PDAM bulan Maret', 1),
('Internet', 500000, '2024-03-05', 'Langganan internet bulanan', 1),
('Gaji', 2500000, '2024-03-01', 'Gaji cleaning service bulan Maret', 1),
('Listrik', 3400000, '2024-04-25', 'Tagihan listrik bulan April 2024', 1),
('Air', 780000, '2024-04-25', 'Tagihan air PDAM bulan April', 1),
('Internet', 500000, '2024-04-05', 'Langganan internet bulanan', 1),
('Perbaikan', 800000, '2024-04-20', 'Service AC kamar 301', 1),
('Listrik', 3500000, '2024-05-25', 'Tagihan listrik bulan Mei 2024', 1),
('Air', 850000, '2024-05-25', 'Tagihan air PDAM bulan Mei', 1),
('Internet', 500000, '2024-05-05', 'Langganan internet bulanan', 1),
('Gaji', 2500000, '2024-05-01', 'Gaji cleaning service bulan Mei', 1),
('Lainnya', 500000, '2024-05-10', 'Pembelian alat kebersihan', 1),
('Listrik', 3700000, '2024-06-25', 'Tagihan listrik bulan Juni 2024', 1),
('Air', 890000, '2024-06-25', 'Tagihan air PDAM bulan Juni', 1),
('Internet', 500000, '2024-06-05', 'Langganan internet bulanan', 1),
('Perbaikan', 2000000, '2024-06-18', 'Renovasi kamar mandi kamar 203', 1),
('Listrik', 3600000, '2024-07-25', 'Tagihan listrik bulan Juli 2024', 1),
('Air', 860000, '2024-07-25', 'Tagihan air PDAM bulan Juli', 1),
('Internet', 500000, '2024-07-05', 'Langganan internet bulanan', 1),
('Gaji', 2500000, '2024-07-01', 'Gaji cleaning service bulan Juli', 1),
('Listrik', 3500000, '2024-08-25', 'Tagihan listrik bulan Agustus 2024', 1),
('Air', 830000, '2024-08-25', 'Tagihan air PDAM bulan Agustus', 1),
('Internet', 500000, '2024-08-05', 'Langganan internet bulanan', 1),
('Perbaikan', 1200000, '2024-08-15', 'Ganti kunci pintu kamar 102 dan 401', 1),
('Listrik', 3650000, '2024-09-25', 'Tagihan listrik bulan September 2024', 1),
('Air', 870000, '2024-09-25', 'Tagihan air PDAM bulan September', 1),
('Internet', 500000, '2024-09-05', 'Langganan internet bulanan', 1),
('Gaji', 2500000, '2024-09-01', 'Gaji cleaning service bulan September', 1),
('Lainnya', 350000, '2024-09-12', 'Pembelian lampu LED untuk koridor', 1);

-- ========================================
-- COMMENTS
-- ========================================
COMMENT ON TABLE pembayaran IS 'Catatan pembayaran sewa bulanan per penghuni';
COMMENT ON TABLE pengeluaran IS 'Catatan pengeluaran operasional kost';
COMMENT ON COLUMN pembayaran.periode_bulan IS '1=Januari, 2=Februari, ..., 12=Desember';
COMMENT ON COLUMN pembayaran.denda IS 'Denda keterlambatan pembayaran (opsional)';
COMMENT ON COLUMN pembayaran.total_bayar IS 'Generated column: nominal + denda';

SELECT 'Database setup complete!' as status;
