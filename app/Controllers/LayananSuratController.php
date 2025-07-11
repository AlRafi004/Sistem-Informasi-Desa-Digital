<?php

namespace App\Controllers;

use App\Models\LayananSurat;

class LayananSuratController
{
    public function index()
    {
        $layanan = new LayananSurat();
        $data = $layanan->getAll();
        $this->viewIndex($data);
    }
    
    private function viewIndex($data)
    {
        echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Layanan Surat - Dashboard Admin</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>
</head>
<body>
    <nav class='navbar navbar-expand-lg navbar-dark bg-primary'>
        <div class='container'>
            <a class='navbar-brand' href='/dashboard'>
                <i class='fas fa-tachometer-alt me-2'></i>Dashboard Admin
            </a>
            <div class='navbar-nav ms-auto'>
                <a class='nav-link' href='/logout'><i class='fas fa-sign-out-alt me-1'></i>Logout</a>
            </div>
        </div>
    </nav>

    <div class='container mt-4'>
        <h2><i class='fas fa-file-alt me-2'></i>Layanan Surat</h2>
        
        <div class='row mb-4'>
            <div class='col-md-3'>
                <div class='card bg-warning text-dark'>
                    <div class='card-body text-center'>
                        <h5>Pending</h5>
                        <h3>" . count(array_filter($data, function($item) { return $item['status'] == 'pending'; })) . "</h3>
                    </div>
                </div>
            </div>
            <div class='col-md-3'>
                <div class='card bg-info text-white'>
                    <div class='card-body text-center'>
                        <h5>Diproses</h5>
                        <h3>" . count(array_filter($data, function($item) { return $item['status'] == 'diproses'; })) . "</h3>
                    </div>
                </div>
            </div>
            <div class='col-md-3'>
                <div class='card bg-success text-white'>
                    <div class='card-body text-center'>
                        <h5>Selesai</h5>
                        <h3>" . count(array_filter($data, function($item) { return $item['status'] == 'selesai'; })) . "</h3>
                    </div>
                </div>
            </div>
            <div class='col-md-3'>
                <div class='card bg-danger text-white'>
                    <div class='card-body text-center'>
                        <h5>Ditolak</h5>
                        <h3>" . count(array_filter($data, function($item) { return $item['status'] == 'ditolak'; })) . "</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class='card'>
            <div class='card-body'>
                <div class='table-responsive'>
                    <table class='table table-striped'>
                        <thead class='table-dark'>
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>NIK Pemohon</th>
                                <th>Nama Pemohon</th>
                                <th>Jenis Surat</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>";
        
        if (empty($data)) {
            echo "<tr><td colspan='7' class='text-center text-muted'>Belum ada pengajuan surat</td></tr>";
        } else {
            foreach ($data as $row) {
                $statusClass = [
                    'pending' => 'warning',
                    'diproses' => 'info',
                    'selesai' => 'success',
                    'ditolak' => 'danger'
                ];
                
                echo "<tr>
                    <td>" . htmlspecialchars($row['nomor_pengajuan']) . "</td>
                    <td>" . htmlspecialchars($row['nik_pemohon']) . "</td>
                    <td>" . htmlspecialchars($row['nama_pemohon']) . "</td>
                    <td>" . ucfirst(htmlspecialchars($row['jenis_surat'])) . "</td>
                    <td>" . htmlspecialchars($row['tanggal_pengajuan']) . "</td>
                    <td><span class='badge bg-" . ($statusClass[$row['status']] ?? 'secondary') . "'>" . ucfirst(htmlspecialchars($row['status'])) . "</span></td>
                    <td>
                        <button class='btn btn-sm btn-info' onclick='viewDetail(\"" . htmlspecialchars($row['nomor_pengajuan']) . "\")'>
                            <i class='fas fa-eye'></i>
                        </button>
                        <button class='btn btn-sm btn-warning' onclick='processRequest(\"" . htmlspecialchars($row['nomor_pengajuan']) . "\")'>
                            <i class='fas fa-cogs'></i>
                        </button>
                    </td>
                </tr>";
            }
        }
        
        echo "        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class='mt-3'>
            <a href='/dashboard' class='btn btn-secondary'>
                <i class='fas fa-arrow-left me-1'></i>Kembali ke Dashboard
            </a>
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
    <script>
        function viewDetail(nomor) {
            alert('Fitur detail pengajuan ' + nomor + ' sedang dalam pengembangan.');
        }
        
        function processRequest(nomor) {
            alert('Fitur proses pengajuan ' + nomor + ' sedang dalam pengembangan.');
        }
    </script>
</body>
</html>";
    }
}
