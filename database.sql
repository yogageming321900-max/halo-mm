-- 1. Membuat Database Baru (Jika belum ada)
CREATE DATABASE IF NOT EXISTS ecoscan_db;
USE ecoscan_db;

-- ========================================================
-- 2. MEMBUAT TABEL USERS (Untuk Akun Login Petugas)
-- ========================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL, -- Disarankan menggunakan password_hash saat di PHP
  `nama_lengkap` VARCHAR(100) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- 3. MEMBUAT TABEL HISTORY_SCANS (Untuk Riwayat Sampah AI)
-- ========================================================
CREATE TABLE IF NOT EXISTS `history_scans` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `benda_nama` VARCHAR(100) NOT NULL,                           -- Contoh: 'Botol Plastik'
  `bahan` ENUM('Plastik', 'Karet', 'Tumbuhan / Alam', 'Kayu / Alam') NOT NULL, 
  `kategori` ENUM('organik', 'anorganik') NOT NULL,             -- Penentu organik/anorganik
  `akurasi` VARCHAR(10) NOT NULL,                               -- Contoh: '94.5%'
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP               -- Waktu otomatis terisi saat scan
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- 4. DATA CONTOH (OPSIONAL) - Agar database tidak kosong
-- ========================================================
INSERT INTO `users` (`username`, `password`, `nama_lengkap`) VALUES
('admin', 'admin123', 'Petugas Kebersihan Utama');

INSERT INTO `history_scans` (`benda_nama`, `bahan`, `kategori`, `akurasi`) VALUES
('Botol Air Mineral', 'Plastik', 'anorganik', '94.5%'),
('Kulit Pisang Raja', 'Tumbuhan / Alam', 'organik', '91.2%'),
('Kardus Mie Instan', 'Kayu / Alam', 'organik', '88.9%');