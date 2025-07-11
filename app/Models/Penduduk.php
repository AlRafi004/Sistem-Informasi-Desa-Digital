<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Helpers\ConfigHelper;
use DateTime;
use Exception;

/*
|--------------------------------------------------------------------------
| Penduduk Model (Custom)
|--------------------------------------------------------------------------
|
| Model untuk mengelola data penduduk dalam sistem custom
| Sistem Informasi Desa Digital
|
*/

class Penduduk
{
    protected $table = 'penduduk';

    protected $fillable = [
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'rt',
        'rw',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'kewarganegaraan',
        'nomor_kk',
        'status_dalam_keluarga',
        'is_active',
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
     * Get all penduduk
     */
    public static function all()
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            return self::getDemoData();
        }

        try {
            $stmt = $pdo->query("SELECT * FROM penduduk ORDER BY nama ASC");
            $results = $stmt->fetchAll();
            
            return array_map(function($row) {
                return new self($row);
            }, $results);
        } catch (PDOException $e) {
            return self::getDemoData();
        }
    }

    /**
     * Find penduduk by NIK
     */
    public static function find($nik)
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            $demoData = self::getDemoData();
            foreach ($demoData as $item) {
                if ($item->nik == $nik) {
                    return $item;
                }
            }
            return null;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM penduduk WHERE nik = ?");
            $stmt->execute([$nik]);
            $result = $stmt->fetch();
            
            return $result ? new self($result) : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Create new penduduk
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
            
            $stmt = $pdo->prepare("INSERT INTO penduduk ({$fields}) VALUES ({$placeholders})");
            return $stmt->execute($data);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update penduduk
     */
    public function update($data)
    {
        $pdo = self::getConnection();
        if (!$pdo || !isset($this->attributes['nik'])) {
            return false;
        }

        try {
            $setParts = [];
            foreach ($data as $key => $value) {
                $setParts[] = "{$key} = :{$key}";
            }
            $setClause = implode(', ', $setParts);
            
            $data['nik'] = $this->attributes['nik'];
            $stmt = $pdo->prepare("UPDATE penduduk SET {$setClause} WHERE nik = :nik");
            
            if ($stmt->execute($data)) {
                foreach ($data as $key => $value) {
                    if ($key !== 'nik') {
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
     * Delete penduduk
     */
    public function delete()
    {
        $pdo = self::getConnection();
        if (!$pdo || !isset($this->attributes['nik'])) {
            return false;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM penduduk WHERE nik = ?");
            return $stmt->execute([$this->attributes['nik']]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get active residents
     */
    public static function aktif()
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            $demoData = self::getDemoData();
            return array_filter($demoData, function($item) {
                return $item->is_active == 1;
            });
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM penduduk WHERE is_active = 1 ORDER BY nama ASC");
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            return array_map(function($row) {
                return new self($row);
            }, $results);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get male residents
     */
    public static function lakiLaki()
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            $demoData = self::getDemoData();
            return array_filter($demoData, function($item) {
                return $item->jenis_kelamin === 'L';
            });
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM penduduk WHERE jenis_kelamin = 'L' ORDER BY nama ASC");
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            return array_map(function($row) {
                return new self($row);
            }, $results);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get female residents
     */
    public static function perempuan()
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            $demoData = self::getDemoData();
            return array_filter($demoData, function($item) {
                return $item->jenis_kelamin === 'P';
            });
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM penduduk WHERE jenis_kelamin = 'P' ORDER BY nama ASC");
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            return array_map(function($row) {
                return new self($row);
            }, $results);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get count by gender
     */
    public static function countByGender($gender)
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            $demoData = self::getDemoData();
            return count(array_filter($demoData, function($item) use ($gender) {
                return $item->jenis_kelamin === $gender;
            }));
        }

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM penduduk WHERE jenis_kelamin = ?");
            $stmt->execute([$gender]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get age from birth date
     */
    public function getUmur()
    {
        if (!$this->tanggal_lahir) {
            return 0;
        }

        try {
            $birthDate = new DateTime($this->tanggal_lahir);
            $today = new DateTime();
            $age = $today->diff($birthDate);
            return $age->y;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get gender text
     */
    public function getJenisKelaminText()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Get formatted birth date
     */
    public function getTanggalLahirFormatted()
    {
        if (!$this->tanggal_lahir) {
            return '-';
        }

        try {
            $date = new DateTime($this->tanggal_lahir);
            return $date->format('d/m/Y');
        } catch (Exception $e) {
            return $this->tanggal_lahir;
        }
    }

    /**
     * Get all penduduk records
     */
    public function getAll()
    {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) {
                return $this->getDemoData();
            }
            
            $stmt = $pdo->query("SELECT * FROM {$this->table} ORDER BY nama_lengkap ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return $this->getDemoData();
        }
    }
    
    /**
     * Save penduduk data
     */
    public function save($data)
    {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) {
                return false;
            }
            
            $sql = "INSERT INTO {$this->table} (
                nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin,
                agama, status_kawin, pekerjaan, alamat, rt, rw, no_kk, status_keluarga
            ) VALUES (
                :nik, :nama_lengkap, :tempat_lahir, :tanggal_lahir, :jenis_kelamin,
                :agama, :status_kawin, :pekerjaan, :alamat, :rt, :rw, :no_kk, :status_keluarga
            )";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute($data);
        } catch (Exception $e) {
            return false;
        }
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
            new self([
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
                'kewarganegaraan' => 'WNI',
                'nomor_kk' => '6201012501850001',
                'status_dalam_keluarga' => 'Kepala Keluarga',
                'is_active' => 1,
            ]),
            new self([
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
                'kewarganegaraan' => 'WNI',
                'nomor_kk' => '6201012501850001',
                'status_dalam_keluarga' => 'Istri',
                'is_active' => 1,
            ]),
            new self([
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
                'kewarganegaraan' => 'WNI',
                'nomor_kk' => '6201013101950003',
                'status_dalam_keluarga' => 'Kepala Keluarga',
                'is_active' => 1,
            ]),
            new self([
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
                'kewarganegaraan' => 'WNI',
                'nomor_kk' => '6201016502920004',
                'status_dalam_keluarga' => 'Kepala Keluarga',
                'is_active' => 1,
            ]),
            new self([
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
                'kewarganegaraan' => 'WNI',
                'nomor_kk' => '6201011503880005',
                'status_dalam_keluarga' => 'Kepala Keluarga',
                'is_active' => 1,
            ]),
        ];
    }
}
