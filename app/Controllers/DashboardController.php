<?php

namespace App\Controllers;

use PDO;
use PDOException;
use App\Helpers\ConfigHelper;

class DashboardController
{
    public function index()
    {
        // Simple check without using Auth facade
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'Admin',
            'role' => $_SESSION['user_role'] ?? 'admin'
        ];
        
        $stats = $this->getStatistics();
        $villageInfo = ConfigHelper::village();
        $appName = ConfigHelper::appName();
        
        $this->view('dashboard/index', [
            'stats' => $stats, 
            'user' => $user,
            'village' => $villageInfo,
            'appName' => $appName
        ]);
    }
    
    private function getStatistics()
    {
        try {
            $db = $this->getDatabase();
            if (!$db) return $this->getDefaultStats();
            
            // Get population statistics
            $totalPenduduk = $db->query("SELECT COUNT(*) as count FROM penduduk")->fetch()['count'] ?? 0;
            $lakiLaki = $db->query("SELECT COUNT(*) as count FROM penduduk WHERE jenis_kelamin = 'L'")->fetch()['count'] ?? 0;
            $perempuan = $db->query("SELECT COUNT(*) as count FROM penduduk WHERE jenis_kelamin = 'P'")->fetch()['count'] ?? 0;
            
            // Get document statistics
            $pengajuanPending = $db->query("SELECT COUNT(*) as count FROM layanan_surat WHERE status = 'pending'")->fetch()['count'] ?? 0;
            $pengajuanSelesai = $db->query("SELECT COUNT(*) as count FROM layanan_surat WHERE status = 'selesai'")->fetch()['count'] ?? 0;
            
            return [
                'total_penduduk' => $totalPenduduk,
                'laki_laki' => $lakiLaki,
                'perempuan' => $perempuan,
                'pengajuan_pending' => $pengajuanPending,
                'pengajuan_selesai' => $pengajuanSelesai,
            ];
        } catch (\Exception $e) {
            return $this->getDefaultStats();
        }
    }
    
    private function getDefaultStats()
    {
        return [
            'total_penduduk' => 2450,
            'laki_laki' => 1280,
            'perempuan' => 1170,
            'pengajuan_pending' => 15,
            'pengajuan_selesai' => 42,
        ];
    }
    
    private function getDatabase()
    {
        try {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $dbname = $_ENV['DB_DATABASE'] ?? 'desa_digital';
            $username = $_ENV['DB_USERNAME'] ?? 'root';
            $password = $_ENV['DB_PASSWORD'] ?? '';
            
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            return new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            return null;
        }
    }
    
    private function view($template, $data = [])
    {
        extract($data);
        echo $this->getSimpleDashboard($data);
    }
    
    private function getSimpleDashboard($data)
    {
        $stats = $data['stats'];
        $user = $data['user'];
        
        $roleDisplay = match($user['role']) {
            'kepala_desa' => 'Kepala Desa',
            'sekretaris' => 'Sekretaris',
            'kaur' => 'Kaur',
            default => 'Admin'
        };
        
        $additionalMenu = '';
        if ($user['role'] === 'kepala_desa') {
            $additionalMenu = "
                        <li class='nav-item'>
                            <a href='/profil-desa' class='nav-link text-white'>
                                <i class='fas fa-cog me-2'></i>Profil Desa
                            </a>
                        </li>";
        }
        
        return "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dashboard - Sistem Informasi Desa Digital</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>
    <style>
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 0.75rem 1rem; margin: 0.25rem 0; border-radius: 0.375rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background-color: rgba(255,255,255,0.1); }
        .main-content { background-color: #f8f9fa; min-height: 100vh; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border-radius: 0.5rem; }
    </style>
</head>
<body>
    <div class='container-fluid'>
        <div class='row'>
            <!-- Sidebar -->
            <div class='col-md-3 col-lg-2 px-0 sidebar'>
                <div class='d-flex flex-column p-3 text-white'>
                    <a href='/dashboard' class='navbar-brand d-flex align-items-center mb-3 text-white text-decoration-none'>
                        <i class='fas fa-home me-2'></i>
                        <span>Desa Digital</span>
                    </a>
                    
                    <ul class='nav nav-pills flex-column mb-auto'>
                        <li class='nav-item'>
                            <a href='/dashboard' class='nav-link active text-white'>
                                <i class='fas fa-tachometer-alt me-2'></i>Dashboard
                            </a>
                        </li>
                        <li class='nav-item'>
                            <a href='/penduduk' class='nav-link text-white'>
                                <i class='fas fa-users me-2'></i>Data Penduduk
                            </a>
                        </li>
                        <li class='nav-item'>
                            <a href='/layanan-surat' class='nav-link text-white'>
                                <i class='fas fa-file-alt me-2'></i>Layanan Surat
                            </a>
                        </li>
                        $additionalMenu
                    </ul>
                    
                    <hr>
                    <div class='dropdown'>
                        <a href='#' class='nav-link dropdown-toggle text-white' data-bs-toggle='dropdown'>
                            <i class='fas fa-user me-2'></i>" . htmlspecialchars($user['name']) . "
                        </a>
                        <ul class='dropdown-menu dropdown-menu-dark'>
                            <li><a class='dropdown-item' href='/logout'>
                                <i class='fas fa-sign-out-alt me-2'></i>Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class='col-md-9 col-lg-10 px-0'>
                <div class='main-content'>
                    <!-- Header -->
                    <nav class='navbar navbar-expand-lg navbar-light bg-white shadow-sm'>
                        <div class='container-fluid'>
                            <h1 class='h4 mb-0'>Dashboard</h1>
                            <div class='navbar-nav ms-auto'>
                                <span class='navbar-text'>
                                    <i class='fas fa-user-tag me-1'></i>$roleDisplay
                                </span>
                            </div>
                        </div>
                    </nav>

                    <!-- Content -->
                    <div class='container-fluid p-4'>
                        <div class='row mb-4'>
                            <!-- Statistics Cards -->
                            <div class='col-lg-3 col-md-6 mb-3'>
                                <div class='card text-center h-100'>
                                    <div class='card-body'>
                                        <div class='text-primary mb-2'>
                                            <i class='fas fa-users fa-2x'></i>
                                        </div>
                                        <h3 class='fw-bold'>" . number_format($stats['total_penduduk']) . "</h3>
                                        <p class='text-muted mb-0'>Total Penduduk</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class='col-lg-3 col-md-6 mb-3'>
                                <div class='card text-center h-100'>
                                    <div class='card-body'>
                                        <div class='text-info mb-2'>
                                            <i class='fas fa-male fa-2x'></i>
                                        </div>
                                        <h3 class='fw-bold'>" . number_format($stats['laki_laki']) . "</h3>
                                        <p class='text-muted mb-0'>Laki-laki</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class='col-lg-3 col-md-6 mb-3'>
                                <div class='card text-center h-100'>
                                    <div class='card-body'>
                                        <div class='text-danger mb-2'>
                                            <i class='fas fa-female fa-2x'></i>
                                        </div>
                                        <h3 class='fw-bold'>" . number_format($stats['perempuan']) . "</h3>
                                        <p class='text-muted mb-0'>Perempuan</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class='col-lg-3 col-md-6 mb-3'>
                                <div class='card text-center h-100'>
                                    <div class='card-body'>
                                        <div class='text-warning mb-2'>
                                            <i class='fas fa-file-alt fa-2x'></i>
                                        </div>
                                        <h3 class='fw-bold'>" . number_format($stats['pengajuan_pending']) . "</h3>
                                        <p class='text-muted mb-0'>Pengajuan Pending</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class='row mb-4'>
                            <div class='col-lg-6 mb-3'>
                                <div class='card h-100'>
                                    <div class='card-header'>
                                        <h5 class='card-title mb-0'>
                                            <i class='fas fa-chart-pie me-2'></i>Distribusi Penduduk
                                        </h5>
                                    </div>
                                    <div class='card-body'>
                                        <canvas id='populationChart'></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class='col-lg-6 mb-3'>
                                <div class='card h-100'>
                                    <div class='card-header'>
                                        <h5 class='card-title mb-0'>
                                            <i class='fas fa-chart-bar me-2'></i>Status Pengajuan Surat
                                        </h5>
                                    </div>
                                    <div class='card-body'>
                                        <canvas id='documentChart'></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class='row'>
                            <div class='col-12'>
                                <div class='card'>
                                    <div class='card-header'>
                                        <h5 class='card-title mb-0'>
                                            <i class='fas fa-bolt me-2'></i>Aksi Cepat
                                        </h5>
                                    </div>
                                    <div class='card-body'>
                                        <div class='row'>
                                            <div class='col-md-3 mb-2'>
                                                <a href='/penduduk/create' class='btn btn-primary w-100'>
                                                    <i class='fas fa-user-plus me-2'></i>Tambah Penduduk
                                                </a>
                                            </div>
                                            <div class='col-md-3 mb-2'>
                                                <a href='/layanan-surat' class='btn btn-success w-100'>
                                                    <i class='fas fa-file-alt me-2'></i>Kelola Surat
                                                </a>
                                            </div>
                                            <div class='col-md-3 mb-2'>
                                                <a href='/laporan' class='btn btn-info w-100'>
                                                    <i class='fas fa-chart-line me-2'></i>Laporan
                                                </a>
                                            </div>
                                            <div class='col-md-3 mb-2'>
                                                <button class='btn btn-warning w-100' onclick='trackStatus()'>
                                                    <i class='fas fa-search me-2'></i>Tracking Status
                                                </button>
                                            </div>
                                            <div class='col-md-3 mb-2'>
                                                <a href='/' class='btn btn-secondary w-100'>
                                                    <i class='fas fa-globe me-2'></i>Lihat Portal
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Applications -->
                        <div class='row mt-4'>
                            <div class='col-12'>
                                <div class='card'>
                                    <div class='card-header'>
                                        <h5 class='card-title mb-0'>
                                            <i class='fas fa-clock me-2'></i>Pengajuan Terbaru
                                        </h5>
                                    </div>
                                    <div class='card-body'>
                                        <div class='table-responsive'>
                                            <table class='table table-hover'>
                                                <thead>
                                                    <tr>
                                                        <th>Nomor</th>
                                                        <th>Jenis Surat</th>
                                                        <th>Pemohon</th>
                                                        <th>Tanggal</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><code>SK001-2024</code></td>
                                                        <td>Surat Keterangan Domisili</td>
                                                        <td>Ahmad Fadli</td>
                                                        <td>10/07/2024</td>
                                                        <td><span class='badge bg-success'>Selesai</span></td>
                                                        <td>
                                                            <button class='btn btn-sm btn-outline-primary' onclick='viewTracking(\"SK001-2024\")'>
                                                                <i class='fas fa-eye'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>SK002-2024</code></td>
                                                        <td>Surat Keterangan Usaha</td>
                                                        <td>Siti Nurhaliza</td>
                                                        <td>11/07/2024</td>
                                                        <td><span class='badge bg-info'>Diproses</span></td>
                                                        <td>
                                                            <button class='btn btn-sm btn-outline-primary' onclick='viewTracking(\"SK002-2024\")'>
                                                                <i class='fas fa-eye'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>SK003-2024</code></td>
                                                        <td>Surat Keterangan Tidak Mampu</td>
                                                        <td>Budi Santoso</td>
                                                        <td>11/07/2024</td>
                                                        <td><span class='badge bg-warning'>Pending</span></td>
                                                        <td>
                                                            <button class='btn btn-sm btn-outline-primary' onclick='viewTracking(\"SK003-2024\")'>
                                                                <i class='fas fa-eye'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class='text-center mt-3'>
                                            <a href='/layanan-surat' class='btn btn-primary'>
                                                <i class='fas fa-list me-1'></i>Lihat Semua Pengajuan
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class='bg-light text-center text-muted py-3 mt-5'>
        <div class='container'>
            <p>&copy; 2025 Sistem Informasi Desa Digital - Kabupaten Katingan</p>
            <p class='small'>Dibuat oleh Muhammad Hadianur Al Rafi</p>
        </div>
    </footer>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
    <script>
        // Population Chart
        const populationCtx = document.getElementById('populationChart').getContext('2d');
        new Chart(populationCtx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [" . $stats['laki_laki'] . ", " . $stats['perempuan'] . "],
                    backgroundColor: ['#36A2EB', '#FF6384'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // Document Status Chart
        const documentCtx = document.getElementById('documentChart').getContext('2d');
        new Chart(documentCtx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Selesai'],
                datasets: [{
                    label: 'Jumlah',
                    data: [" . $stats['pengajuan_pending'] . ", " . $stats['pengajuan_selesai'] . "],
                    backgroundColor: ['#FFA726', '#4CAF50'],
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
        
        function trackStatus() {
            const nomor = prompt('Masukkan nomor pengajuan untuk dilacak:');
            if (nomor) {
                window.open('/tracking/' + nomor, '_blank');
            }
        }
        
        function viewTracking(nomor) {
            window.open('/tracking/' + nomor, '_blank');
        }
    </script>
</body>
</html>";
    }
}
