<?php

namespace Database\Seeders;

/*
|--------------------------------------------------------------------------
| Database Seeder (Custom)
|--------------------------------------------------------------------------
|
| This file provides sample data seeding for the custom application.
| Actual data seeding is handled via SQL file: database/desa_digital.sql
|
*/

class DatabaseSeeder
{
    public function run()
    {
        echo "Database Seeder for Sistem Informasi Desa Digital\n";
        echo "============================================\n\n";
        
        echo "Sample data is available in: database/desa_digital.sql\n";
        echo "To seed the database:\n";
        echo "1. Import database/desa_digital.sql into MySQL\n";
        echo "2. Or run the SQL queries manually in phpMyAdmin\n\n";
        
        echo "Default login credentials:\n";
        echo "- Kepala Desa: kepala@desa.com / password\n";
        echo "- Sekretaris: sekretaris@desa.com / password\n";
        echo "- Kaur: kaur@desa.com / password\n\n";
        
        echo "Database includes:\n";
        echo "- 3 default users with different roles\n";
        echo "- 1 village profile (Desa Tumbang Jutuh)\n";
        echo "- 5 sample residents\n";
        echo "- 3 sample document requests\n\n";
        
        echo "Note: This application uses custom models, not Laravel Eloquent.\n";
    }
    
    /**
     * Get sample data for manual insertion
     */
    public static function getSampleUsers()
    {
        return [
            [
                'name' => 'Kepala Desa',
                'email' => 'kepala@desa.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'kepala_desa',
                'phone' => '081234567890',
            ],
            [
                'name' => 'Sekretaris Desa',
                'email' => 'sekretaris@desa.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'sekretaris',
                'phone' => '081234567891',
            ],
            [
                'name' => 'Kaur Pemerintahan',
                'email' => 'kaur@desa.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'kaur',
                'phone' => '081234567892',
            ],
        ];
    }
    
    /**
     * Get village profile data
     */
    public static function getVillageProfile()
    {
        return [
            'nama_desa' => 'Desa Tumbang Jutuh',
            'kode_desa' => '62.01.01.2001',
            'kepala_desa' => 'Bapak Sukirman',
            'alamat' => 'Jalan Raya Tumbang Jutuh No. 1',
            'kecamatan' => 'Katingan Hulu',
            'kabupaten' => 'Kabupaten Katingan',
            'provinsi' => 'Kalimantan Tengah',
            'kode_pos' => '74411',
            'luas_wilayah' => 125.50,
            'jumlah_penduduk' => 2450,
            'jumlah_kk' => 650,
            'batas_utara' => 'Desa Tumbang Miwan',
            'batas_selatan' => 'Desa Tumbang Rungan',
            'batas_timur' => 'Desa Petak Bahandang',
            'batas_barat' => 'Desa Tumbang Senamang',
            'visi' => 'Mewujudkan Desa Tumbang Jutuh yang maju, mandiri, dan sejahtera berbasis kearifan lokal',
            'misi' => 'Meningkatkan kualitas pelayanan publik, pemberdayaan masyarakat, dan pengembangan potensi desa',
            'sejarah' => 'Desa Tumbang Jutuh didirikan pada tahun 1960 dan merupakan salah satu desa tertua di Kecamatan Katingan Hulu.',
            'potensi_wisata' => 'Wisata alam sungai Katingan, hutan lindung, dan budaya tradisional Dayak',
            'potensi_umkm' => 'Kerajinan anyaman rotan, budidaya ikan, perkebunan karet, dan usaha warung makan',
        ];
    }
    
    /**
     * Get sample residents data
     */
    public static function getSampleResidents()
    {
        return [
            [
                'nik' => '6201012501850001',
                'nama' => 'Budi Santoso',
                'tempat_lahir' => 'Katingan',
                'tanggal_lahir' => '1985-01-25',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jalan Merdeka No. 12 RT 001 RW 001',
                'rt' => '001',
                'rw' => '001',
                'agama' => 'Islam',
                'status_perkawinan' => 'Kawin',
                'pekerjaan' => 'Petani',
                'nomor_kk' => '6201012501850001',
                'status_dalam_keluarga' => 'Kepala Keluarga',
            ],
            [
                'nik' => '6201014502900002',
                'nama' => 'Siti Aminah',
                'tempat_lahir' => 'Katingan',
                'tanggal_lahir' => '1990-02-05',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jalan Merdeka No. 12 RT 001 RW 001',
                'rt' => '001',
                'rw' => '001',
                'agama' => 'Islam',
                'status_perkawinan' => 'Kawin',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'nomor_kk' => '6201012501850001',
                'status_dalam_keluarga' => 'Istri',
            ],
            [
                'nik' => '6201013101950003',
                'nama' => 'Ahmad Fauzi',
                'tempat_lahir' => 'Katingan',
                'tanggal_lahir' => '1995-01-31',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jalan Gotong Royong No. 5 RT 002 RW 001',
                'rt' => '002',
                'rw' => '001',
                'agama' => 'Islam',
                'status_perkawinan' => 'Belum Kawin',
                'pekerjaan' => 'Mahasiswa',
                'nomor_kk' => '6201013101950003',
                'status_dalam_keluarga' => 'Kepala Keluarga',
            ],
            [
                'nik' => '6201016502920004',
                'nama' => 'Maria Ulina',
                'tempat_lahir' => 'Katingan',
                'tanggal_lahir' => '1992-02-05',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jalan Pemuda No. 8 RT 003 RW 002',
                'rt' => '003',
                'rw' => '002',
                'agama' => 'Kristen',
                'status_perkawinan' => 'Kawin',
                'pekerjaan' => 'Guru',
                'nomor_kk' => '6201016502920004',
                'status_dalam_keluarga' => 'Kepala Keluarga',
            ],
            [
                'nik' => '6201011503880005',
                'nama' => 'Rudi Hartono',
                'tempat_lahir' => 'Katingan',
                'tanggal_lahir' => '1988-03-15',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jalan Veteran No. 15 RT 001 RW 003',
                'rt' => '001',
                'rw' => '003',
                'agama' => 'Kristen',
                'status_perkawinan' => 'Kawin',
                'pekerjaan' => 'Wiraswasta',
                'nomor_kk' => '6201011503880005',
                'status_dalam_keluarga' => 'Kepala Keluarga',
            ],
        ];
    }
}
