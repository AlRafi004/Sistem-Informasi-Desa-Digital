<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Helpers\ConfigHelper;

/*
|--------------------------------------------------------------------------
| LayananSurat Model (Custom)
|--------------------------------------------------------------------------
|
| Model untuk mengelola data layanan surat dalam sistem custom
| Sistem Informasi Desa Digital
|
*/

class LayananSurat
{
    protected $table = 'layanan_surat';
    
    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_DIPROSES = 'diproses';
    const STATUS_SELESAI = 'selesai';
    const STATUS_DITOLAK = 'ditolak';

    // Jenis surat constants
    const JENIS_DOMISILI = 'domisili';
    const JENIS_USAHA = 'usaha';
    const JENIS_KETERANGAN = 'keterangan';
    const JENIS_SKCK = 'skck';

    protected $fillable = [
        'nomor_pengajuan',
        'nik_pemohon',
        'nama_pemohon',
        'jenis_surat',
        'keperluan',
        'keterangan',
        'file_pendukung',
        'status',
        'tanggal_pengajuan',
        'tanggal_selesai',
        'file_surat',
        'processed_by',
        'catatan',
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
            // Return null if database not available (fallback to demo data)
            return null;
        }
    }

    /**
     * Get all layanan surat
     */
    public static function all()
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            return self::getDemoData();
        }

        try {
            $stmt = $pdo->query("SELECT * FROM layanan_surat ORDER BY tanggal_pengajuan DESC");
            $results = $stmt->fetchAll();
            
            return array_map(function($row) {
                return new self($row);
            }, $results);
        } catch (PDOException $e) {
            return self::getDemoData();
        }
    }

    /**
     * Find layanan surat by ID
     */
    public static function find($id)
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            $demoData = self::getDemoData();
            foreach ($demoData as $item) {
                if ($item->id == $id) {
                    return $item;
                }
            }
            return null;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM layanan_surat WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            return $result ? new self($result) : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get layanan surat by status
     */
    public static function whereStatus($status)
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            $demoData = self::getDemoData();
            return array_filter($demoData, function($item) use ($status) {
                return $item->status === $status;
            });
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM layanan_surat WHERE status = ? ORDER BY tanggal_pengajuan DESC");
            $stmt->execute([$status]);
            $results = $stmt->fetchAll();
            
            return array_map(function($row) {
                return new self($row);
            }, $results);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Create new layanan surat
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
            
            $stmt = $pdo->prepare("INSERT INTO layanan_surat ({$fields}) VALUES ({$placeholders})");
            return $stmt->execute($data);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update layanan surat
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
            $stmt = $pdo->prepare("UPDATE layanan_surat SET {$setClause} WHERE id = :id");
            
            if ($stmt->execute($data)) {
                // Update local attributes
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
     * Delete layanan surat
     */
    public function delete()
    {
        $pdo = self::getConnection();
        if (!$pdo || !isset($this->attributes['id'])) {
            return false;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM layanan_surat WHERE id = ?");
            return $stmt->execute([$this->attributes['id']]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get pending requests
     */
    public static function pending()
    {
        return self::whereStatus(self::STATUS_PENDING);
    }

    /**
     * Get completed requests
     */
    public static function selesai()
    {
        return self::whereStatus(self::STATUS_SELESAI);
    }

    /**
     * Get count by status
     */
    public static function countByStatus($status)
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            $demoData = self::getDemoData();
            return count(array_filter($demoData, function($item) use ($status) {
                return $item->status === $status;
            }));
        }

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM layanan_surat WHERE status = ?");
            $stmt->execute([$status]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Get status badge class
     */
    public function getStatusBadge()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_DIPROSES => 'info',
            self::STATUS_SELESAI => 'success',
            self::STATUS_DITOLAK => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get jenis surat display name
     */
    public function getJenisSuratDisplay()
    {
        return match($this->jenis_surat) {
            self::JENIS_DOMISILI => 'Surat Keterangan Domisili',
            self::JENIS_USAHA => 'Surat Keterangan Usaha',
            self::JENIS_KETERANGAN => 'Surat Keterangan Umum',
            self::JENIS_SKCK => 'Surat Keterangan Catatan Kepolisian',
            default => 'Surat Lainnya'
        };
    }

    /**
     * Get status display
     */
    public function getStatusDisplay()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_DIPROSES => 'Sedang Diproses',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DITOLAK => 'Ditolak',
            default => 'Unknown'
        };
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
            [
                'id' => 1,
                'nomor_pengajuan' => 'PGJ-20240710-ABC123',
                'nik_pemohon' => '6201012501850001',
                'nama_pemohon' => 'Budi Santoso',
                'jenis_surat' => self::JENIS_DOMISILI,
                'keperluan' => 'Persyaratan melamar pekerjaan',
                'keterangan' => 'Diperlukan untuk melengkapi berkas lamaran kerja',
                'status' => self::STATUS_SELESAI,
                'tanggal_pengajuan' => '2024-07-10 09:00:00',
                'tanggal_selesai' => '2024-07-11 14:30:00',
                'processed_by' => 2,
                'file_pendukung' => null,
                'file_surat' => 'surat_domisili_budi.pdf',
                'catatan' => 'Surat telah selesai diproses',
            ],
            [
                'id' => 2,
                'nomor_pengajuan' => 'PGJ-20240711-DEF456',
                'nik_pemohon' => '6201016502920004',
                'nama_pemohon' => 'Maria Ulina',
                'jenis_surat' => self::JENIS_USAHA,
                'keperluan' => 'Mengurus izin usaha warung',
                'keterangan' => 'Untuk membuka warung makan di depan rumah',
                'status' => self::STATUS_DIPROSES,
                'tanggal_pengajuan' => '2024-07-11 10:15:00',
                'tanggal_selesai' => null,
                'processed_by' => 3,
                'file_pendukung' => 'ktp_maria.jpg',
                'file_surat' => null,
                'catatan' => 'Sedang dalam proses verifikasi',
            ],
            [
                'id' => 3,
                'nomor_pengajuan' => 'PGJ-20240711-GHI789',
                'nik_pemohon' => '6201013101950003',
                'nama_pemohon' => 'Ahmad Fauzi',
                'jenis_surat' => self::JENIS_KETERANGAN,
                'keperluan' => 'Persyaratan beasiswa',
                'keterangan' => 'Surat keterangan tidak mampu untuk beasiswa kuliah',
                'status' => self::STATUS_PENDING,
                'tanggal_pengajuan' => '2024-07-11 11:30:00',
                'tanggal_selesai' => null,
                'processed_by' => null,
                'file_pendukung' => 'kk_ahmad.jpg',
                'file_surat' => null,
                'catatan' => null,
            ],
        ];
    }

    /**
     * Get all layanan surat records
     */
    public function getAll()
    {
        try {
            $pdo = $this->getConnection();
            if (!$pdo) {
                return $this->getDemoData();
            }
            
            $stmt = $pdo->query("SELECT * FROM {$this->table} ORDER BY tanggal_pengajuan DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return $this->getDemoData();
        }
    }
}
