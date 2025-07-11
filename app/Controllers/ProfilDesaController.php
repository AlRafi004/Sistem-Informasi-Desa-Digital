<?php

namespace App\Controllers;

use App\Models\ProfilDesa;

class ProfilDesaController
{
    public function index()
    {
        $profil = new ProfilDesa();
        $data = $profil->first();
        $this->viewIndex($data);
    }
    
    private function viewIndex($data)
    {
        echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Profil Desa - Dashboard Admin</title>
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
        <h2><i class='fas fa-building me-2'></i>Profil Desa</h2>
        
        <div class='card'>
            <div class='card-body'>
                <form method='POST' action='/profil-desa/update'>
                    <div class='row'>
                        <div class='col-md-6'>
                            <div class='mb-3'>
                                <label for='nama_desa' class='form-label'>Nama Desa</label>
                                <input type='text' class='form-control' id='nama_desa' name='nama_desa' 
                                       value='" . htmlspecialchars($data['nama_desa'] ?? 'Desa Tumbang Jutuh') . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='kecamatan' class='form-label'>Kecamatan</label>
                                <input type='text' class='form-control' id='kecamatan' name='kecamatan' 
                                       value='" . htmlspecialchars($data['kecamatan'] ?? 'Katingan Hulu') . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='kabupaten' class='form-label'>Kabupaten</label>
                                <input type='text' class='form-control' id='kabupaten' name='kabupaten' 
                                       value='" . htmlspecialchars($data['kabupaten'] ?? 'Kabupaten Katingan') . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='provinsi' class='form-label'>Provinsi</label>
                                <input type='text' class='form-control' id='provinsi' name='provinsi' 
                                       value='" . htmlspecialchars($data['provinsi'] ?? 'Kalimantan Tengah') . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='kode_pos' class='form-label'>Kode Pos</label>
                                <input type='text' class='form-control' id='kode_pos' name='kode_pos' 
                                       value='" . htmlspecialchars($data['kode_pos'] ?? '74364') . "' required>
                            </div>
                        </div>
                        
                        <div class='col-md-6'>
                            <div class='mb-3'>
                                <label for='luas_wilayah' class='form-label'>Luas Wilayah (km²)</label>
                                <input type='number' step='0.01' class='form-control' id='luas_wilayah' name='luas_wilayah' 
                                       value='" . htmlspecialchars($data['luas_wilayah'] ?? '125.50') . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='jumlah_penduduk' class='form-label'>Jumlah Penduduk</label>
                                <input type='number' class='form-control' id='jumlah_penduduk' name='jumlah_penduduk' 
                                       value='" . htmlspecialchars($data['jumlah_penduduk'] ?? '2450') . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='jumlah_kk' class='form-label'>Jumlah KK</label>
                                <input type='number' class='form-control' id='jumlah_kk' name='jumlah_kk' 
                                       value='" . htmlspecialchars($data['jumlah_kk'] ?? '650') . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='kepala_desa' class='form-label'>Kepala Desa</label>
                                <input type='text' class='form-control' id='kepala_desa' name='kepala_desa' 
                                       value='" . htmlspecialchars($data['kepala_desa'] ?? 'H. Ahmad Yani, S.Pd') . "' required>
                            </div>
                            <div class='mb-3'>
                                <label for='sekretaris_desa' class='form-label'>Sekretaris Desa</label>
                                <input type='text' class='form-control' id='sekretaris_desa' name='sekretaris_desa' 
                                       value='" . htmlspecialchars($data['sekretaris_desa'] ?? 'Budi Santoso, S.Sos') . "' required>
                            </div>
                        </div>
                    </div>
                    
                    <div class='mb-3'>
                        <label for='visi' class='form-label'>Visi Desa</label>
                        <textarea class='form-control' id='visi' name='visi' rows='3' required>" . 
                        htmlspecialchars($data['visi'] ?? 'Terwujudnya Desa Tumbang Jutuh yang maju, mandiri, dan sejahtera berdasarkan nilai-nilai gotong royong dan kearifan lokal.') . 
                        "</textarea>
                    </div>
                    
                    <div class='mb-3'>
                        <label for='misi' class='form-label'>Misi Desa</label>
                        <textarea class='form-control' id='misi' name='misi' rows='5' required>" . 
                        htmlspecialchars($data['misi'] ?? '1. Meningkatkan kualitas pelayanan publik yang prima kepada masyarakat
2. Mengembangkan potensi ekonomi lokal untuk kesejahteraan masyarakat
3. Melestarikan budaya dan kearifan lokal Dayak Katingan
4. Membangun infrastruktur desa yang mendukung kemajuan ekonomi
5. Menciptakan tata kelola pemerintahan yang transparan dan akuntabel') . 
                        "</textarea>
                    </div>
                    
                    <div class='mb-3'>
                        <label for='alamat_kantor' class='form-label'>Alamat Kantor Desa</label>
                        <textarea class='form-control' id='alamat_kantor' name='alamat_kantor' rows='2' required>" . 
                        htmlspecialchars($data['alamat_kantor'] ?? 'Jl. Trans Kalimantan KM 15, Tumbang Jutuh, Katingan Hulu, Kabupaten Katingan, Kalimantan Tengah') . 
                        "</textarea>
                    </div>
                    
                    <div class='row'>
                        <div class='col-md-6'>
                            <div class='mb-3'>
                                <label for='telepon' class='form-label'>Telepon</label>
                                <input type='text' class='form-control' id='telepon' name='telepon' 
                                       value='" . htmlspecialchars($data['telepon'] ?? '+62 XXX-XXXX-XXXX') . "'>
                            </div>
                        </div>
                        <div class='col-md-6'>
                            <div class='mb-3'>
                                <label for='email' class='form-label'>Email</label>
                                <input type='email' class='form-control' id='email' name='email' 
                                       value='" . htmlspecialchars($data['email'] ?? 'desa@tumbangjiutuh.go.id') . "'>
                            </div>
                        </div>
                    </div>
                    
                    <div class='row mt-3'>
                        <div class='col-12'>
                            <button type='submit' class='btn btn-success me-2'>
                                <i class='fas fa-save me-1'></i>Simpan Perubahan
                            </button>
                            <a href='/dashboard' class='btn btn-secondary'>
                                <i class='fas fa-arrow-left me-1'></i>Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>    </div>

    <!-- Footer -->
    <footer class='bg-light text-center text-muted py-3 mt-5'>
        <div class='container'>
            <p>&copy; 2025 Sistem Informasi Desa Digital - Kabupaten Katingan</p>
            <p class='small'>Dibuat oleh Muhammad Hadianur Al Rafi</p>
        </div>
    </footer>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            alert('Fitur update profil desa sedang dalam pengembangan.');
            e.preventDefault();
        });
    </script>
</body>
</html>";
    }
}
