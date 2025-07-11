<?php

namespace App\Helpers;

class DatabaseHelper
{
    private $pdo;
    
    public function __construct()
    {
        $this->initializeDatabase();
    }
    
    private function initializeDatabase()
    {
        try {
            $config = require dirname(__DIR__, 2) . '/config/app.php';
            $dbConfig = $config['database'];
            
            $host = $dbConfig['host'];
            $dbname = $dbConfig['database'];
            $username = $dbConfig['username'];
            $password = $dbConfig['password'];
            
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $this->pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
            
            // Create tables if they don't exist
            $this->createTables();
            
        } catch (\PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            // Fall back to file-based storage
            $this->pdo = null;
        }
    }
    
    private function createTables()
    {
        if (!$this->pdo) return;
        
        try {
            // Create layanan_surat table
            $sql = "CREATE TABLE IF NOT EXISTS layanan_surat (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nomor_pengajuan VARCHAR(50) UNIQUE NOT NULL,
                nama_pemohon VARCHAR(255) NOT NULL,
                nik VARCHAR(16) NOT NULL,
                jenis_surat VARCHAR(100) NOT NULL,
                keperluan TEXT NOT NULL,
                alamat TEXT NOT NULL,
                catatan TEXT,
                file_pendukung VARCHAR(255),
                google_drive_id VARCHAR(255),
                google_drive_link TEXT,
                tanggal_pengajuan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                tanggal_selesai TIMESTAMP NULL,
                status ENUM('pending', 'diproses', 'selesai', 'ditolak') DEFAULT 'pending',
                keterangan TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            $this->pdo->exec($sql);
            
            // Create upload_logs table
            $sql = "CREATE TABLE IF NOT EXISTS upload_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nomor_pengajuan VARCHAR(50),
                filename VARCHAR(255),
                file_size INT,
                file_type VARCHAR(100),
                local_path TEXT,
                google_drive_id VARCHAR(255),
                google_drive_status ENUM('pending', 'uploaded', 'failed') DEFAULT 'pending',
                upload_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                error_message TEXT
            )";
            
            $this->pdo->exec($sql);
            
        } catch (\PDOException $e) {
            error_log("Table creation failed: " . $e->getMessage());
        }
    }
    
    public function savePengajuan($data)
    {
        if ($this->pdo) {
            return $this->savePengajuanToDatabase($data);
        } else {
            return $this->savePengajuanToFile($data);
        }
    }
    
    private function savePengajuanToDatabase($data)
    {
        try {
            $sql = "INSERT INTO layanan_surat (
                nomor_pengajuan, nama_pemohon, nik, jenis_surat, keperluan, 
                alamat, catatan, file_pendukung, google_drive_id, google_drive_link,
                tanggal_pengajuan, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['nomor_pengajuan'],
                $data['nama_pemohon'],
                $data['nik'],
                $data['jenis_surat'],
                $data['keperluan'],
                $data['alamat'],
                $data['catatan'] ?? '',
                $data['file_pendukung'] ?? '',
                $data['google_drive_id'] ?? '',
                $data['google_drive_link'] ?? '',
                $data['tanggal_pengajuan'],
                $data['status']
            ]);
            
            if ($result) {
                return ['success' => true, 'id' => $this->pdo->lastInsertId()];
            } else {
                throw new \Exception("Gagal menyimpan ke database");
            }
            
        } catch (\PDOException $e) {
            error_log("Database save error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function savePengajuanToFile($data)
    {
        try {
            $dataFile = dirname(__DIR__, 2) . '/storage/data/pengajuan.json';
            $dataDir = dirname($dataFile);
            
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0755, true);
            }
            
            // Load existing data
            $existingData = [];
            if (file_exists($dataFile)) {
                $content = file_get_contents($dataFile);
                $existingData = json_decode($content, true) ?: [];
            }
            
            // Add new data
            $existingData[] = $data;
            
            // Save back to file
            $result = file_put_contents($dataFile, json_encode($existingData, JSON_PRETTY_PRINT));
            
            if ($result !== false) {
                return ['success' => true, 'id' => count($existingData)];
            } else {
                throw new \Exception("Gagal menyimpan ke file");
            }
            
        } catch (\Exception $e) {
            error_log("File save error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function logUpload($data)
    {
        if ($this->pdo) {
            try {
                $sql = "INSERT INTO upload_logs (
                    nomor_pengajuan, filename, file_size, file_type, 
                    local_path, google_drive_id, google_drive_status
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    $data['nomor_pengajuan'],
                    $data['filename'],
                    $data['file_size'],
                    $data['file_type'],
                    $data['local_path'],
                    $data['google_drive_id'] ?? '',
                    $data['google_drive_status'] ?? 'pending'
                ]);
            } catch (\PDOException $e) {
                error_log("Upload log error: " . $e->getMessage());
                return false;
            }
        }
        return true; // Silent fail if no database
    }
    
    public function getPengajuan($nomorPengajuan)
    {
        if ($this->pdo) {
            try {
                $sql = "SELECT * FROM layanan_surat WHERE nomor_pengajuan = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$nomorPengajuan]);
                return $stmt->fetch();
            } catch (\PDOException $e) {
                error_log("Get pengajuan error: " . $e->getMessage());
                return null;
            }
        } else {
            // Read from file
            $dataFile = dirname(__DIR__, 2) . '/storage/data/pengajuan.json';
            if (file_exists($dataFile)) {
                $content = file_get_contents($dataFile);
                $data = json_decode($content, true) ?: [];
                
                foreach ($data as $item) {
                    if ($item['nomor_pengajuan'] === $nomorPengajuan) {
                        return $item;
                    }
                }
            }
            return null;
        }
    }
    
    public function getAllPengajuan()
    {
        if ($this->pdo) {
            try {
                $sql = "SELECT * FROM layanan_surat ORDER BY tanggal_pengajuan DESC";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll();
            } catch (\PDOException $e) {
                error_log("Get all pengajuan error: " . $e->getMessage());
                return [];
            }
        } else {
            // Read from file
            $dataFile = dirname(__DIR__, 2) . '/storage/data/pengajuan.json';
            if (file_exists($dataFile)) {
                $content = file_get_contents($dataFile);
                $data = json_decode($content, true) ?: [];
                
                // Sort by date descending
                usort($data, function($a, $b) {
                    return strtotime($b['tanggal_pengajuan']) - strtotime($a['tanggal_pengajuan']);
                });
                
                return $data;
            }
            return [];
        }
    }
    
    public function updateStatus($nomorPengajuan, $status, $keterangan = null)
    {
        if ($this->pdo) {
            try {
                $sql = "UPDATE layanan_surat SET status = ?, keterangan = ?, updated_at = CURRENT_TIMESTAMP";
                $params = [$status, $keterangan];
                
                if ($status === 'selesai') {
                    $sql .= ", tanggal_selesai = CURRENT_TIMESTAMP";
                }
                
                $sql .= " WHERE nomor_pengajuan = ?";
                $params[] = $nomorPengajuan;
                
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($params);
            } catch (\PDOException $e) {
                error_log("Update status error: " . $e->getMessage());
                return false;
            }
        }
        return true; // Silent success if no database
    }
}
