-- Database setup for Sistem Informasi Desa Digital
-- Run this SQL script to create the database structure and sample data

-- Create database
CREATE DATABASE IF NOT EXISTS desa_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE desa_digital;

-- Create users table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('kepala_desa', 'sekretaris', 'kaur') NOT NULL,
    phone VARCHAR(15) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create penduduk table
CREATE TABLE penduduk (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(16) UNIQUE NOT NULL,
    nama VARCHAR(255) NOT NULL,
    tempat_lahir VARCHAR(255) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    alamat TEXT NOT NULL,
    rt VARCHAR(3) NOT NULL,
    rw VARCHAR(3) NOT NULL,
    agama VARCHAR(255) NOT NULL,
    status_perkawinan VARCHAR(255) NOT NULL,
    pekerjaan VARCHAR(255) NOT NULL,
    kewarganegaraan VARCHAR(255) DEFAULT 'WNI',
    nomor_kk VARCHAR(16) NOT NULL,
    status_dalam_keluarga VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nik_active (nik, is_active),
    INDEX idx_nomor_kk (nomor_kk)
);

-- Create layanan_surat table
CREATE TABLE layanan_surat (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor_pengajuan VARCHAR(255) UNIQUE NOT NULL,
    nik_pemohon VARCHAR(16) NOT NULL,
    nama_pemohon VARCHAR(255) NOT NULL,
    jenis_surat ENUM('domisili', 'usaha', 'keterangan', 'skck') NOT NULL,
    keperluan TEXT NOT NULL,
    keterangan TEXT NULL,
    file_pendukung VARCHAR(255) NULL,
    status ENUM('pending', 'diproses', 'selesai', 'ditolak') DEFAULT 'pending',
    tanggal_pengajuan TIMESTAMP NOT NULL,
    tanggal_selesai TIMESTAMP NULL,
    file_surat VARCHAR(255) NULL,
    processed_by BIGINT UNSIGNED NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nik_pemohon) REFERENCES penduduk(nik),
    FOREIGN KEY (processed_by) REFERENCES users(id),
    INDEX idx_status_tanggal (status, tanggal_pengajuan)
);

-- Create profil_desa table
CREATE TABLE profil_desa (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_desa VARCHAR(255) NOT NULL,
    kode_desa VARCHAR(255) UNIQUE NOT NULL,
    kepala_desa VARCHAR(255) NOT NULL,
    alamat TEXT NOT NULL,
    kecamatan VARCHAR(255) NOT NULL,
    kabupaten VARCHAR(255) NOT NULL,
    provinsi VARCHAR(255) NOT NULL,
    kode_pos VARCHAR(5) NOT NULL,
    luas_wilayah DECIMAL(8,2) NOT NULL,
    jumlah_penduduk INT NOT NULL,
    jumlah_kk INT NOT NULL,
    batas_utara VARCHAR(255) NULL,
    batas_selatan VARCHAR(255) NULL,
    batas_timur VARCHAR(255) NULL,
    batas_barat VARCHAR(255) NULL,
    visi TEXT NULL,
    misi TEXT NULL,
    sejarah TEXT NULL,
    potensi_wisata TEXT NULL,
    potensi_umkm TEXT NULL,
    logo_desa VARCHAR(255) NULL,
    foto_kantor VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data

-- Insert users
INSERT INTO users (name, email, password, role, phone) VALUES
('Kepala Desa', 'kepala@desa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kepala_desa', '081234567890'),
('Sekretaris Desa', 'sekretaris@desa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sekretaris', '081234567891'),
('Kaur Pemerintahan', 'kaur@desa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kaur', '081234567892');

-- Insert profil desa
INSERT INTO profil_desa (
    nama_desa, kode_desa, kepala_desa, alamat, kecamatan, kabupaten, provinsi, kode_pos,
    luas_wilayah, jumlah_penduduk, jumlah_kk, batas_utara, batas_selatan, batas_timur, batas_barat,
    visi, misi, sejarah, potensi_wisata, potensi_umkm
) VALUES (
    'Desa Tumbang Jutuh',
    '62.01.01.2001',
    'Bapak Sukirman',
    'Jalan Raya Tumbang Jutuh No. 1',
    'Katingan Hulu',
    'Kabupaten Katingan',
    'Kalimantan Tengah',
    '74411',
    125.50,
    2450,
    650,
    'Desa Tumbang Miwan',
    'Desa Tumbang Rungan',
    'Desa Petak Bahandang',
    'Desa Tumbang Senamang',
    'Mewujudkan Desa Tumbang Jutuh yang maju, mandiri, dan sejahtera berbasis kearifan lokal',
    'Meningkatkan kualitas pelayanan publik, pemberdayaan masyarakat, dan pengembangan potensi desa',
    'Desa Tumbang Jutuh didirikan pada tahun 1960 dan merupakan salah satu desa tertua di Kecamatan Katingan Hulu.',
    'Wisata alam sungai Katingan, hutan lindung, dan budaya tradisional Dayak',
    'Kerajinan anyaman rotan, budidaya ikan, perkebunan karet, dan usaha warung makan'
);

-- Insert sample penduduk
INSERT INTO penduduk (nik, nama, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, rt, rw, agama, status_perkawinan, pekerjaan, nomor_kk, status_dalam_keluarga) VALUES
('6201012501850001', 'Budi Santoso', 'Katingan', '1985-01-25', 'L', 'Jalan Merdeka No. 12 RT 001 RW 001', '001', '001', 'Islam', 'Kawin', 'Petani', '6201012501850001', 'Kepala Keluarga'),
('6201014502900002', 'Siti Aminah', 'Katingan', '1990-02-05', 'P', 'Jalan Merdeka No. 12 RT 001 RW 001', '001', '001', 'Islam', 'Kawin', 'Ibu Rumah Tangga', '6201012501850001', 'Istri'),
('6201013101950003', 'Ahmad Fauzi', 'Katingan', '1995-01-31', 'L', 'Jalan Gotong Royong No. 5 RT 002 RW 001', '002', '001', 'Islam', 'Belum Kawin', 'Mahasiswa', '6201013101950003', 'Kepala Keluarga'),
('6201016502920004', 'Maria Ulina', 'Katingan', '1992-02-05', 'P', 'Jalan Pemuda No. 8 RT 003 RW 002', '003', '002', 'Kristen', 'Kawin', 'Guru', '6201016502920004', 'Kepala Keluarga'),
('6201011503880005', 'Rudi Hartono', 'Katingan', '1988-03-15', 'L', 'Jalan Veteran No. 15 RT 001 RW 003', '001', '003', 'Kristen', 'Kawin', 'Wiraswasta', '6201011503880005', 'Kepala Keluarga');

-- Insert sample layanan surat
INSERT INTO layanan_surat (nomor_pengajuan, nik_pemohon, nama_pemohon, jenis_surat, keperluan, keterangan, status, tanggal_pengajuan, tanggal_selesai, processed_by) VALUES
('PGJ-20240710-ABC123', '6201012501850001', 'Budi Santoso', 'domisili', 'Persyaratan melamar pekerjaan', 'Diperlukan untuk melengkapi berkas lamaran kerja', 'selesai', '2024-07-10 09:00:00', '2024-07-11 14:30:00', 2),
('PGJ-20240711-DEF456', '6201016502920004', 'Maria Ulina', 'usaha', 'Mengurus izin usaha warung', 'Untuk membuka warung makan di depan rumah', 'diproses', '2024-07-11 10:15:00', NULL, 3),
('PGJ-20240711-GHI789', '6201013101950003', 'Ahmad Fauzi', 'keterangan', 'Persyaratan beasiswa', 'Surat keterangan tidak mampu untuk beasiswa kuliah', 'pending', '2024-07-11 11:30:00', NULL, NULL);
