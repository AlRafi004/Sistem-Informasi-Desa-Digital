<?php

namespace App\Controllers;

class AdminController
{
    public function dashboard()
    {
        $this->viewDashboard();
    }
    
    public function pengajuan()
    {
        $this->viewPengajuan();
    }
    
    private function viewDashboard()
    {
        echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sistem Informasi Desa Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin">
                <i class="fas fa-cog me-2"></i>Admin Dashboard
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin">Dashboard</a>
                <a class="nav-link" href="/admin/pengajuan">Pengajuan</a>
                <a class="nav-link" href="/">Kembali ke Site</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Dashboard Admin</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-file-alt me-2"></i>Kelola Pengajuan
                        </h5>
                        <p class="card-text">Lihat dan kelola semua pengajuan surat yang masuk.</p>
                        <a href="/admin/pengajuan" class="btn btn-primary">Lihat Pengajuan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
    }
    
    private function viewPengajuan()
    {
        // Include database helper
        require_once dirname(__DIR__) . '/Helpers/DatabaseHelper.php';
        $database = new \App\Helpers\DatabaseHelper();
        
        // Get all pengajuan from database or file
        $pengajuanList = $this->getAllPengajuan($database);
        
        echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengajuan - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .google-drive-link {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/admin">
                <i class="fas fa-cog me-2"></i>Admin Dashboard
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/admin">Dashboard</a>
                <a class="nav-link active" href="/admin/pengajuan">Pengajuan</a>
                <a class="nav-link" href="/">Kembali ke Site</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Kelola Pengajuan Surat</h2>
            <span class="badge bg-primary fs-6">' . count($pengajuanList) . ' Total Pengajuan</span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Nama Pemohon</th>
                        <th>Jenis Surat</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>File Lokal</th>
                        <th>Google Drive</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($pengajuanList)) {
            echo '<tr><td colspan="8" class="text-center text-muted">Belum ada pengajuan</td></tr>';
        } else {
            foreach ($pengajuanList as $pengajuan) {
                $statusClass = match($pengajuan['status']) {
                    'pending' => 'warning',
                    'diproses' => 'info',
                    'selesai' => 'success',
                    'ditolak' => 'danger',
                    default => 'secondary'
                };
                
                echo '<tr>
                    <td><strong>' . htmlspecialchars($pengajuan['nomor_pengajuan']) . '</strong></td>
                    <td>' . htmlspecialchars($pengajuan['nama_pemohon']) . '</td>
                    <td>' . htmlspecialchars($pengajuan['jenis_surat']) . '</td>
                    <td>' . date('d/m/Y H:i', strtotime($pengajuan['tanggal_pengajuan'])) . '</td>
                    <td><span class="badge bg-' . $statusClass . '">' . ucfirst($pengajuan['status']) . '</span></td>
                    <td>';
                
                if (!empty($pengajuan['file_pendukung'])) {
                    $filePath = 'uploads/pendukung/' . $pengajuan['file_pendukung'];
                    echo '<a href="/' . $filePath . '" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download me-1"></i>Download
                    </a>';
                } else {
                    echo '<span class="text-muted">-</span>';
                }
                
                echo '</td>
                    <td>';
                
                if (!empty($pengajuan['google_drive_link'])) {
                    echo '<a href="' . htmlspecialchars($pengajuan['google_drive_link']) . '" target="_blank" class="btn btn-sm btn-success">
                        <i class="fab fa-google-drive me-1"></i>Buka
                    </a>';
                } else {
                    echo '<span class="text-muted">Tidak ada</span>';
                }
                
                echo '</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="/tracking/' . htmlspecialchars($pengajuan['nomor_pengajuan']) . '" class="btn btn-outline-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-outline-warning" onclick="updateStatus(\'' . htmlspecialchars($pengajuan['nomor_pengajuan']) . '\')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </td>
                </tr>';
            }
        }
        
        echo '</tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-3 mt-5">
        <div class="container">
            <p>&copy; 2025 Sistem Informasi Desa Digital - Kabupaten Katingan</p>
            <p class="small">Dibuat oleh Muhammad Hadianur Al Rafi</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateStatus(nomorPengajuan) {
            const newStatus = prompt("Masukkan status baru (pending/diproses/selesai/ditolak):");
            if (newStatus && ["pending", "diproses", "selesai", "ditolak"].includes(newStatus)) {
                // Here you would implement the AJAX call to update status
                alert("Fitur update status akan segera tersedia");
            }
        }
    </script>
</body>
</html>';
    }
    
    private function getAllPengajuan($database)
    {
        // Try to get from database first
        try {
            if (method_exists($database, 'getAllPengajuan')) {
                return $database->getAllPengajuan();
            }
        } catch (\Exception $e) {
            error_log("Get all pengajuan error: " . $e->getMessage());
        }
        
        // Fallback to file-based storage
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
