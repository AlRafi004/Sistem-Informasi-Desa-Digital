<?php

/*
|--------------------------------------------------------------------------
| Database Migration Utility (Custom)
|--------------------------------------------------------------------------
|
| This file contains all database creation scripts for the
| Sistem Informasi Desa Digital application.
|
| Usage:
| - php DatabaseMigration.php setup    - Create all tables and seed data
| - php DatabaseMigration.php drop     - Drop all tables
| - php DatabaseMigration.php users    - Create users table only
| - php DatabaseMigration.php status   - Check database status
|
| Note: For complete setup, use: database/desa_digital.sql
|
*/

class DatabaseMigration
{
    private $pdo;
    private $config;

    public function __construct()
    {
        $this->config = [
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'desa_digital',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4'
        ];
    }

    private function connect()
    {
        if ($this->pdo) {
            return $this->pdo;
        }

        try {
            $dsn = "mysql:host={$this->config['host']};port={$this->config['port']};dbname={$this->config['database']};charset={$this->config['charset']}";
            $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Create all tables and seed data
     */
    public function setup()
    {
        echo "🚀 Setting up Sistem Informasi Desa Digital Database...\n\n";
        
        $this->createUsersTable();
        $this->createProfilDesaTable();
        $this->createPendudukTable();
        $this->createLayananSuratTable();
        $this->seedData();
        
        echo "\n✅ Database setup completed successfully!\n";
        echo "📋 Default login credentials:\n";
        echo "   - Kepala Desa: kepala@desa.com / password\n";
        echo "   - Sekretaris: sekretaris@desa.com / password\n";
        echo "   - Kaur: kaur@desa.com / password\n\n";
    }

    /**
     * Create users table
     */
    public function createUsersTable()
    {
        echo "📋 Creating users table...\n";
        
        $sql = "
            CREATE TABLE IF NOT EXISTS `users` (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `email_verified_at` timestamp NULL DEFAULT NULL,
                `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `role` enum('kepala_desa','sekretaris','kaur') COLLATE utf8mb4_unicode_ci NOT NULL,
                `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_email_unique` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $this->connect()->exec($sql);
        echo "   ✅ Users table created\n";
    }

    /**
     * Create profil_desa table
     */
    public function createProfilDesaTable()
    {
        echo "🏘️ Creating profil_desa table...\n";
        
        $sql = "
            CREATE TABLE IF NOT EXISTS `profil_desa` (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `nama_desa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `kode_desa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `kepala_desa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
                `kecamatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `kabupaten` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `provinsi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `kode_pos` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `luas_wilayah` decimal(10,2) NOT NULL,
                `jumlah_penduduk` int(11) NOT NULL,
                `jumlah_kk` int(11) NOT NULL,
                `batas_utara` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `batas_selatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `batas_timur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `batas_barat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `visi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `misi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `sejarah` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `potensi_wisata` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `potensi_umkm` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `logo_desa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `foto_kantor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $this->connect()->exec($sql);
        echo "   ✅ Profil desa table created\n";
    }

    /**
     * Create penduduk table
     */
    public function createPendudukTable()
    {
        echo "👥 Creating penduduk table...\n";
        
        $sql = "
            CREATE TABLE IF NOT EXISTS `penduduk` (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `nik` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
                `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `tempat_lahir` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `tanggal_lahir` date NOT NULL,
                `jenis_kelamin` enum('L','P') COLLATE utf8mb4_unicode_ci NOT NULL,
                `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
                `rt` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
                `rw` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
                `agama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `status_perkawinan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `pekerjaan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `kewarganegaraan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WNI',
                `nomor_kk` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
                `status_dalam_keluarga` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `penduduk_nik_unique` (`nik`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $this->connect()->exec($sql);
        echo "   ✅ Penduduk table created\n";
    }

    /**
     * Create layanan_surat table
     */
    public function createLayananSuratTable()
    {
        echo "📄 Creating layanan_surat table...\n";
        
        $sql = "
            CREATE TABLE IF NOT EXISTS `layanan_surat` (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `nomor_pengajuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `nik_pemohon` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
                `nama_pemohon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `jenis_surat` enum('domisili','usaha','keterangan','skck') COLLATE utf8mb4_unicode_ci NOT NULL,
                `keperluan` text COLLATE utf8mb4_unicode_ci NOT NULL,
                `keterangan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `file_pendukung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `status` enum('pending','diproses','selesai','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
                `tanggal_pengajuan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `tanggal_selesai` timestamp NULL DEFAULT NULL,
                `file_surat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
                `catatan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `layanan_surat_nomor_pengajuan_unique` (`nomor_pengajuan`),
                KEY `layanan_surat_nik_pemohon_foreign` (`nik_pemohon`),
                KEY `layanan_surat_processed_by_foreign` (`processed_by`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $this->connect()->exec($sql);
        echo "   ✅ Layanan surat table created\n";
    }

    /**
     * Seed initial data
     */
    public function seedData()
    {
        echo "🌱 Seeding initial data...\n";
        
        // Insert users
        $sql = "
            INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
            (1, 'Kepala Desa', 'kepala@desa.com', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'kepala_desa', '081234567890', 1, NOW(), NOW()),
            (2, 'Sekretaris Desa', 'sekretaris@desa.com', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'sekretaris', '081234567891', 1, NOW(), NOW()),
            (3, 'Kaur Pemerintahan', 'kaur@desa.com', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'kaur', '081234567892', 1, NOW(), NOW());
        ";
        $this->connect()->exec($sql);
        echo "   ✅ Users seeded\n";

        // Insert profil desa
        $sql = "
            INSERT IGNORE INTO `profil_desa` (`id`, `nama_desa`, `kode_desa`, `kepala_desa`, `alamat`, `kecamatan`, `kabupaten`, `provinsi`, `kode_pos`, `luas_wilayah`, `jumlah_penduduk`, `jumlah_kk`, `batas_utara`, `batas_selatan`, `batas_timur`, `batas_barat`, `visi`, `misi`, `sejarah`, `potensi_wisata`, `potensi_umkm`, `created_at`, `updated_at`) VALUES
            (1, 'Desa Tumbang Jutuh', '62.01.01.2001', 'Bapak Sukirman', 'Jalan Raya Tumbang Jutuh No. 1', 'Katingan Hulu', 'Kabupaten Katingan', 'Kalimantan Tengah', '74411', 125.50, 2450, 650, 'Desa Tumbang Miwan', 'Desa Tumbang Rungan', 'Desa Petak Bahandang', 'Desa Tumbang Senamang', 'Mewujudkan Desa Tumbang Jutuh yang maju, mandiri, dan sejahtera berbasis kearifan lokal', 'Meningkatkan kualitas pelayanan publik, pemberdayaan masyarakat, dan pengembangan potensi desa', 'Desa Tumbang Jutuh didirikan pada tahun 1960 dan merupakan salah satu desa tertua di Kecamatan Katingan Hulu.', 'Wisata alam sungai Katingan, hutan lindung, dan budaya tradisional Dayak', 'Kerajinan anyaman rotan, budidaya ikan, perkebunan karet, dan usaha warung makan', NOW(), NOW());
        ";
        $this->connect()->exec($sql);
        echo "   ✅ Profil desa seeded\n";

        // Insert sample penduduk
        $sql = "
            INSERT IGNORE INTO `penduduk` (`nik`, `nama`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `rt`, `rw`, `agama`, `status_perkawinan`, `pekerjaan`, `nomor_kk`, `status_dalam_keluarga`, `created_at`, `updated_at`) VALUES
            ('6201012501850001', 'Budi Santoso', 'Katingan', '1985-01-25', 'L', 'Jalan Merdeka No. 12 RT 001 RW 001', '001', '001', 'Islam', 'Kawin', 'Petani', '6201012501850001', 'Kepala Keluarga', NOW(), NOW()),
            ('6201014502900002', 'Siti Aminah', 'Katingan', '1990-02-05', 'P', 'Jalan Merdeka No. 12 RT 001 RW 001', '001', '001', 'Islam', 'Kawin', 'Ibu Rumah Tangga', '6201012501850001', 'Istri', NOW(), NOW()),
            ('6201013101950003', 'Ahmad Fauzi', 'Katingan', '1995-01-31', 'L', 'Jalan Gotong Royong No. 5 RT 002 RW 001', '002', '001', 'Islam', 'Belum Kawin', 'Mahasiswa', '6201013101950003', 'Kepala Keluarga', NOW(), NOW()),
            ('6201016502920004', 'Maria Ulina', 'Katingan', '1992-02-05', 'P', 'Jalan Pemuda No. 8 RT 003 RW 002', '003', '002', 'Kristen', 'Kawin', 'Guru', '6201016502920004', 'Kepala Keluarga', NOW(), NOW()),
            ('6201011503880005', 'Rudi Hartono', 'Katingan', '1988-03-15', 'L', 'Jalan Veteran No. 15 RT 001 RW 003', '001', '003', 'Kristen', 'Kawin', 'Wiraswasta', '6201011503880005', 'Kepala Keluarga', NOW(), NOW());
        ";
        $this->connect()->exec($sql);
        echo "   ✅ Sample penduduk seeded\n";

        // Insert sample layanan surat
        $sql = "
            INSERT IGNORE INTO `layanan_surat` (`nomor_pengajuan`, `nik_pemohon`, `nama_pemohon`, `jenis_surat`, `keperluan`, `keterangan`, `status`, `tanggal_pengajuan`, `tanggal_selesai`, `processed_by`, `file_surat`, `catatan`, `created_at`, `updated_at`) VALUES
            ('PGJ-20240710-ABC123', '6201012501850001', 'Budi Santoso', 'domisili', 'Persyaratan melamar pekerjaan', 'Diperlukan untuk melengkapi berkas lamaran kerja', 'selesai', '2024-07-10 09:00:00', '2024-07-11 14:30:00', 2, 'surat_domisili_budi.pdf', 'Surat telah selesai diproses', NOW(), NOW()),
            ('PGJ-20240711-DEF456', '6201016502920004', 'Maria Ulina', 'usaha', 'Mengurus izin usaha warung', 'Untuk membuka warung makan di depan rumah', 'diproses', '2024-07-11 10:15:00', NULL, 3, NULL, 'Sedang dalam proses verifikasi', NOW(), NOW()),
            ('PGJ-20240711-GHI789', '6201013101950003', 'Ahmad Fauzi', 'keterangan', 'Persyaratan beasiswa', 'Surat keterangan tidak mampu untuk beasiswa kuliah', 'pending', '2024-07-11 11:30:00', NULL, NULL, NULL, NULL, NOW(), NOW());
        ";
        $this->connect()->exec($sql);
        echo "   ✅ Sample layanan surat seeded\n";
    }

    /**
     * Drop all tables
     */
    public function dropAll()
    {
        echo "⚠️ Dropping all tables...\n";
        
        $tables = ['layanan_surat', 'penduduk', 'profil_desa', 'users'];
        
        foreach ($tables as $table) {
            $this->connect()->exec("DROP TABLE IF EXISTS `{$table}`");
            echo "   ❌ Dropped {$table} table\n";
        }
        
        echo "\n🗑️ All tables dropped successfully!\n";
    }

    /**
     * Check database status
     */
    public function status()
    {
        echo "📊 Database Status:\n\n";
        
        try {
            $this->connect();
            echo "✅ Database connection: OK\n";
            
            $tables = ['users', 'profil_desa', 'penduduk', 'layanan_surat'];
            
            foreach ($tables as $table) {
                try {
                    $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM `{$table}`");
                    $count = $stmt->fetch()['count'];
                    echo "📋 {$table}: {$count} records\n";
                } catch (PDOException $e) {
                    echo "❌ {$table}: Table not found\n";
                }
            }
            
        } catch (Exception $e) {
            echo "❌ Database connection: FAILED\n";
            echo "   Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
}

// Command line interface
if (php_sapi_name() === 'cli') {
    $migration = new DatabaseMigration();
    $action = $argv[1] ?? 'help';
    
    switch ($action) {
        case 'setup':
            $migration->setup();
            break;
            
        case 'drop':
            $migration->dropAll();
            break;
            
        case 'users':
            $migration->createUsersTable();
            break;
            
        case 'status':
            $migration->status();
            break;
            
        case 'help':
        default:
            echo "📚 Database Migration Utility - Sistem Informasi Desa Digital\n\n";
            echo "Usage:\n";
            echo "  php DatabaseMigration.php setup   - Create all tables and seed data\n";
            echo "  php DatabaseMigration.php drop    - Drop all tables\n";
            echo "  php DatabaseMigration.php users   - Create users table only\n";
            echo "  php DatabaseMigration.php status  - Check database status\n";
            echo "  php DatabaseMigration.php help    - Show this help\n\n";
            echo "For complete setup, you can also use: database/desa_digital.sql\n\n";
            break;
    }
}
