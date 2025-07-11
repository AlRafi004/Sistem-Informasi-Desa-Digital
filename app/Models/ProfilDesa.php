<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Helpers\ConfigHelper;
use Exception;

/*
|--------------------------------------------------------------------------
| ProfilDesa Model (Custom)
|--------------------------------------------------------------------------
|
| Model untuk mengelola data profil desa dalam sistem custom
| Sistem Informasi Desa Digital
|
*/

class ProfilDesa
{
    protected $table = 'profil_desa';

    protected $fillable = [
        'nama_desa',
        'kode_desa',
        'kepala_desa',
        'alamat',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'luas_wilayah',
        'jumlah_penduduk',
        'jumlah_kk',
        'batas_utara',
        'batas_selatan',
        'batas_timur',
        'batas_barat',
        'visi',
        'misi',
        'sejarah',
        'potensi_wisata',
        'potensi_umkm',
        'logo_desa',
        'foto_kantor',
    ];

    private $attributes = [];

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Get database connection
     */
    private static function getConnection()
    {
        try {
            $config = ConfigHelper::database();
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            
            $pdo = new PDO($dsn, $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $pdo;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get village profile (singleton)
     */
    public static function getProfile()
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            return self::getDemoData();
        }

        try {
            $stmt = $pdo->query("SELECT * FROM profil_desa LIMIT 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: self::getDemoData();
        } catch (PDOException $e) {
            return self::getDemoData();
        }
    }

    /**
     * Get first (main) profil desa record
     */
    public function first()
    {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) {
                return $this->getDemoData();
            }
            
            $stmt = $pdo->query("SELECT * FROM {$this->table} LIMIT 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: $this->getDemoData();
        } catch (Exception $e) {
            return $this->getDemoData();
        }
    }
    
    /**
     * Update profile
     */
    public function update($data)
    {
        $pdo = self::getConnection();
        if (!$pdo || !isset($this->attributes['id'])) {
            return false;
        }

        try {
            $setParts = [];
            foreach ($data as $key => $value) {
                $setParts[] = "{$key} = :{$key}";
            }
            $setClause = implode(', ', $setParts);
            
            $data['id'] = $this->attributes['id'];
            $stmt = $pdo->prepare("UPDATE profil_desa SET {$setClause} WHERE id = :id");
            
            if ($stmt->execute($data)) {
                foreach ($data as $key => $value) {
                    if ($key !== 'id') {
                        $this->attributes[$key] = $value;
                    }
                }
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create new profile
     */
    public static function create($data)
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            return false;
        }

        try {
            $fields = implode(',', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $stmt = $pdo->prepare("INSERT INTO profil_desa ({$fields}) VALUES ({$placeholders})");
            return $stmt->execute($data);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get formatted area
     */
    public function getFormattedLuasWilayah()
    {
        return number_format($this->luas_wilayah, 2, ',', '.') . ' km²';
    }

    /**
     * Get formatted population
     */
    public function getFormattedJumlahPenduduk()
    {
        return number_format($this->jumlah_penduduk, 0, ',', '.');
    }

    /**
     * Get formatted family count
     */
    public function getFormattedJumlahKk()
    {
        return number_format($this->jumlah_kk, 0, ',', '.');
    }

    /**
     * Get village address
     */
    public function getFullAddress()
    {
        return trim("{$this->alamat}, {$this->kecamatan}, {$this->kabupaten}, {$this->provinsi} {$this->kode_pos}");
    }

    /**
     * Get village boundaries
     */
    public function getBoundaries()
    {
        return [
            'utara' => $this->batas_utara,
            'selatan' => $this->batas_selatan,
            'timur' => $this->batas_timur,
            'barat' => $this->batas_barat,
        ];
    }

    /**
     * Magic getter for attributes
     */
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Magic setter for attributes
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Magic isset for attributes
     */
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Convert to array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Get demo data when database is not available
     */
    private static function getDemoData()
    {
        return [
            'id' => 1,
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
            'logo_desa' => null,
            'foto_kantor' => null,
            'created_at' => '2024-01-01 10:00:00',
            'updated_at' => '2024-01-01 10:00:00',
        ];
    }
}
