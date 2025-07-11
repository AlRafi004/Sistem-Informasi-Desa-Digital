<?php

namespace App\Controllers;

class PublicController
{
    public function index()
    {
        $profil = $this->getProfilDesa();
        $this->view('public/index', ['profil' => $profil]);
    }
    
    public function layanan()
    {
        $this->viewLayanan();
    }
    
    public function profil()
    {
        $profil = $this->getProfilDesa();
        $this->viewProfil($profil);
    }
    
    public function tracking($tracking_number)
    {
        $this->viewTracking($tracking_number);
    }
    
    private function getProfilDesa()
    {
        // Return default data if database connection fails
        return [
            'nama_desa' => 'Desa Tumbang Jutuh',
            'kecamatan' => 'Katingan Hulu',
            'kabupaten' => 'Kabupaten Katingan',
            'jumlah_penduduk' => 2450,
            'jumlah_kk' => 650,
            'luas_wilayah' => 125.50
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
            return new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
        } catch (\PDOException $e) {
            return null;
        }
    }
    
    private function view($template, $data = [])
    {
        extract($data);
        echo $this->getSimpleHomepage($data);
    }
    
    private function getSimpleHomepage($data = [])
    {
        $profil = $data['profil'] ?? null;
        
        $html = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Desa Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-home me-2 text-primary"></i>Desa Digital
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Beranda</a>
                <a class="nav-link" href="/profil">Profil Desa</a>
                <a class="nav-link" href="/layanan">Layanan</a>
                <a class="nav-link" href="/login">Login Admin</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">Selamat Datang di Portal Desa Digital</h1>
                    <p class="lead mb-4">
                        Sistem Informasi Desa Digital Kabupaten Katingan - 
                        Memudahkan pelayanan administrasi dan informasi desa dalam satu platform.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="/layanan" class="btn btn-light btn-lg">
                            <i class="fas fa-file-alt me-2"></i>Ajukan Surat
                        </a>
                        <a href="/profil" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-info-circle me-2"></i>Profil Desa
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-home fa-10x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container py-5">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">Layanan Unggulan</h2>
                <p class="text-muted">Berbagai layanan digital untuk memudahkan warga desa</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="text-primary mb-3">
                            <i class="fas fa-file-alt fa-3x"></i>
                        </div>
                        <h5 class="card-title">Pengajuan Surat Online</h5>
                        <p class="card-text text-muted">
                            Ajukan berbagai jenis surat keterangan secara online dengan upload file pendukung. 
                            File akan disimpan otomatis ke database dan Google Drive untuk keamanan.
                        </p>
                        <a href="/layanan" class="btn btn-primary">Mulai Pengajuan</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="text-success mb-3">
                            <i class="fas fa-search fa-3x"></i>
                        </div>
                        <h5 class="card-title">Tracking Status</h5>
                        <p class="card-text text-muted">
                            Pantau status pengajuan surat Anda secara real-time dengan nomor pengajuan.
                        </p>
                        <button class="btn btn-success" onclick="trackStatus()">Cek Status</button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="text-info mb-3">
                            <i class="fas fa-map-marker-alt fa-3x"></i>
                        </div>
                        <h5 class="card-title">Profil Desa</h5>
                        <p class="card-text text-muted">
                            Informasi lengkap tentang profil desa, demografi, dan potensi yang dimiliki.
                        </p>
                        <a href="/profil" class="btn btn-info">Lihat Profil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>';

        if ($profil) {
            $formatted_penduduk = number_format($profil['jumlah_penduduk'] ?? 0);
            $formatted_kk = number_format($profil['jumlah_kk'] ?? 0);
            $formatted_luas = number_format($profil['luas_wilayah'] ?? 0, 2) . ' km²';
            
            $html .= '
    <!-- Statistics Section -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-12 mb-4">
                    <h3 class="fw-bold">' . htmlspecialchars($profil['nama_desa']) . '</h3>
                    <p class="text-muted">' . htmlspecialchars($profil['kecamatan'] . ', ' . $profil['kabupaten']) . '</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="display-4 fw-bold text-primary">' . $formatted_penduduk . '</div>
                    <div class="text-muted">Total Penduduk</div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="display-4 fw-bold text-success">' . $formatted_kk . '</div>
                    <div class="text-muted">Kepala Keluarga</div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="display-4 fw-bold text-info">' . $formatted_luas . '</div>
                    <div class="text-muted">Luas Wilayah</div>
                </div>
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="display-4 fw-bold text-warning">24/7</div>
                    <div class="text-muted">Layanan Online</div>
                </div>
            </div>
        </div>
    </div>';
        }
        
        $html .= '
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2025 Sistem Informasi Desa Digital - Kabupaten Katingan</p>
            <p class="small text-muted mb-3">Dibuat oleh Muhammad Hadianur Al Rafi</p>
            <p class="text-muted">
                <i class="fas fa-phone me-2"></i>+62 XXX-XXXX-XXXX | 
                <i class="fas fa-envelope me-2"></i>info@desadigital.com
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function trackStatus() {
            const nomor = prompt("Masukkan nomor pengajuan Anda:");
            if (nomor) {
                window.location.href = "/tracking/" + nomor;
            }
        }
    </script>
</body>
</html>';
        
        return $html;
    }
    
    private function viewLayanan()
    {
        echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Publik - Sistem Informasi Desa Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-village me-2"></i>Desa Digital
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Beranda</a>
                <a class="nav-link active" href="/layanan">Layanan</a>
                <a class="nav-link" href="/profil">Profil Desa</a>
                <a class="nav-link" href="/login">Login Admin</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center mb-5">Layanan Publik Desa</h1>
        
        <!-- Form Pengajuan Surat -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Form Pengajuan Surat</h5>
                    </div>
                    <div class="card-body">
                        <form id="formPengajuan" action="/submit-pengajuan" method="POST" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nik" name="nik" maxlength="16" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="jenis_surat" class="form-label">Jenis Surat <span class="text-danger">*</span></label>
                                    <select class="form-select" id="jenis_surat" name="jenis_surat" required>
                                        <option value="">Pilih Jenis Surat</option>
                                        <option value="domisili">Surat Keterangan Domisili</option>
                                        <option value="usaha">Surat Keterangan Usaha</option>
                                        <option value="penghasilan">Surat Keterangan Penghasilan</option>
                                        <option value="tidak_mampu">Surat Keterangan Tidak Mampu</option>
                                        <option value="nikah">Surat Pengantar Nikah</option>
                                        <option value="kelahiran">Surat Keterangan Kelahiran</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="keperluan" class="form-label">Keperluan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="keperluan" name="keperluan" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="file_pendukung" class="form-label">Upload File Pendukung (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="file_pendukung" name="file_pendukung" accept=".pdf" required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    File yang diupload harus berformat PDF dengan ukuran maksimal 5MB. 
                                    File pendukung bisa berupa KTP, KK, atau dokumen lain yang diperlukan.
                                </div>
                                <div id="fileInfo" class="mt-2"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Catatan atau keterangan tambahan (opsional)"></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Ajukan Sekarang
                                </button>
                                <button type="reset" class="btn btn-outline-secondary btn-lg ms-2">
                                    <i class="fas fa-undo me-2"></i>Reset Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Layanan Surat -->
        <div class="row mb-4">
            <div class="col-12 text-center mb-4">
                <h3 class="fw-bold">Jenis Layanan Surat</h3>
                <p class="text-muted">Berbagai jenis surat yang dapat diajukan melalui sistem online</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                        <h5>Surat Keterangan Domisili</h5>
                    </div>
                    <div class="card-body">
                        <p>Surat keterangan tempat tinggal untuk keperluan administrasi.</p>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="fas fa-check me-1"></i>KTP</li>
                            <li><i class="fas fa-check me-1"></i>Kartu Keluarga</li>
                        </ul>
                        <button class="btn btn-primary w-100" onclick="scrollToForm()">Ajukan Online</button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-success text-white text-center">
                        <i class="fas fa-briefcase fa-2x mb-2"></i>
                        <h5>Surat Keterangan Usaha</h5>
                    </div>
                    <div class="card-body">
                        <p>Surat keterangan untuk keperluan perizinan usaha dan bisnis.</p>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="fas fa-check me-1"></i>KTP</li>
                            <li><i class="fas fa-check me-1"></i>Dokumen Usaha</li>
                        </ul>
                        <button class="btn btn-success w-100" onclick="scrollToForm()">Ajukan Online</button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-warning text-white text-center">
                        <i class="fas fa-money-bill fa-2x mb-2"></i>
                        <h5>Surat Keterangan Penghasilan</h5>
                    </div>
                    <div class="card-body">
                        <p>Surat keterangan penghasilan untuk berbagai keperluan administrasi.</p>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="fas fa-check me-1"></i>KTP</li>
                            <li><i class="fas fa-check me-1"></i>Slip Gaji/Bukti</li>
                        </ul>
                        <button class="btn btn-warning w-100" onclick="scrollToForm()">Ajukan Online</button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-danger text-white text-center">
                        <i class="fas fa-heart fa-2x mb-2"></i>
                        <h5>Surat Keterangan Tidak Mampu</h5>
                    </div>
                    <div class="card-body">
                        <p>Surat keterangan kondisi ekonomi untuk bantuan sosial.</p>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="fas fa-check me-1"></i>KTP & KK</li>
                            <li><i class="fas fa-check me-1"></i>Surat Pernyataan</li>
                        </ul>
                        <button class="btn btn-danger w-100" onclick="scrollToForm()">Ajukan Online</button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-info text-white text-center">
                        <i class="fas fa-ring fa-2x mb-2"></i>
                        <h5>Surat Pengantar Nikah</h5>
                    </div>
                    <div class="card-body">
                        <p>Surat pengantar untuk keperluan pernikahan di KUA.</p>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="fas fa-check me-1"></i>KTP & KK</li>
                            <li><i class="fas fa-check me-1"></i>Surat Keterangan</li>
                        </ul>
                        <button class="btn btn-info w-100" onclick="scrollToForm()">Ajukan Online</button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-secondary text-white text-center">
                        <i class="fas fa-baby fa-2x mb-2"></i>
                        <h5>Surat Keterangan Kelahiran</h5>
                    </div>
                    <div class="card-body">
                        <p>Surat keterangan kelahiran untuk pengurusan akta kelahiran.</p>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="fas fa-check me-1"></i>KTP & KK</li>
                            <li><i class="fas fa-check me-1"></i>Surat Keterangan RS</li>
                        </ul>
                        <button class="btn btn-secondary w-100" onclick="scrollToForm()">Ajukan Online</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="card bg-light shadow-sm">
                    <div class="card-body text-center">
                        <h5><i class="fas fa-search me-2"></i>Lacak Status Pengajuan</h5>
                        <p class="text-muted">Masukkan nomor pengajuan untuk melacak status surat Anda</p>
                        <button class="btn btn-primary" onclick="trackStatus()">Lacak Status</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2025 Sistem Informasi Desa Digital - Kabupaten Katingan</p>
            <p class="text-muted small mt-2">
                <i class="fas fa-code me-1"></i>Dibuat oleh Muhammad Hadianur Al Rafi
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload validation and preview
        document.getElementById("file_pendukung").addEventListener("change", function(e) {
            const file = e.target.files[0];
            const fileInfo = document.getElementById("fileInfo");
            
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
                const fileName = file.name;
                
                // Validate file type
                if (file.type !== "application/pdf") {
                    fileInfo.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>File harus berformat PDF!</div>`;
                    e.target.value = "";
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    fileInfo.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Ukuran file maksimal 5MB!</div>`;
                    e.target.value = "";
                    return;
                }
                
                // Show file info
                fileInfo.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>File valid:</strong> ${fileName} (${fileSize} MB)
                    </div>
                `;
            } else {
                fileInfo.innerHTML = "";
            }
        });
        
        // Form submission handler
        document.getElementById("formPengajuan").addEventListener("submit", function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Basic validation
            const nama = formData.get("nama");
            const nik = formData.get("nik");
            const jenis_surat = formData.get("jenis_surat");
            const file = formData.get("file_pendukung");
            
            if (!nama || !nik || !jenis_surat || !file) {
                alert("Mohon lengkapi semua field yang wajib diisi!");
                return;
            }
            
            // NIK validation (must be 16 digits)
            if (!/^\d{16}$/.test(nik)) {
                alert("NIK harus terdiri dari 16 digit angka!");
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector("button[type=submit]");
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = "<i class=\"fas fa-spinner fa-spin me-2\"></i>Mengirim...";
            submitBtn.disabled = true;
            
            // Submit form using fetch API
            fetch("/submit-pengajuan", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset form
                    this.reset();
                    document.getElementById("fileInfo").innerHTML = "";
                    
                    // Prepare detailed success message
                    let successMessage = "Pengajuan berhasil dikirim!\\n\\n";
                    successMessage += "Nomor Pengajuan: " + data.tracking_number + "\\n";
                    successMessage += "Ukuran File: " + data.details.file_size + "\\n\\n";
                    
                    // Add upload status details
                    successMessage += "Status Upload:\\n";
                    successMessage += "• File Lokal: ✓ Tersimpan\\n";
                    successMessage += "• Database: " + (data.details.database_saved ? "✓ Tersimpan" : "✗ Gagal") + "\\n";
                    successMessage += "• Google Drive: " + (data.details.google_drive_upload ? "✓ Berhasil" : "✗ Gagal") + "\\n\\n";
                    
                    if (data.google_drive_link) {
                        successMessage += "Link Google Drive: " + data.google_drive_link + "\\n\\n";
                    }
                    
                    successMessage += "Simpan nomor pengajuan untuk melacak status.";
                    
                    // Show detailed success message
                    alert(successMessage);
                    
                    // Show additional option for Google Drive link
                    if (data.google_drive_link) {
                        if (confirm("File berhasil diupload ke Google Drive. Apakah Anda ingin membuka link file?")) {
                            window.open(data.google_drive_link, "_blank");
                        }
                    }
                    
                    // Redirect to tracking page
                    if (confirm("Apakah Anda ingin langsung melihat status pengajuan?")) {
                        window.location.href = "/tracking/" + data.tracking_number;
                    }
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Terjadi kesalahan saat mengirim pengajuan. Silakan coba lagi.");
            })
            .finally(() => {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
        
        // NIK input validation (numbers only)
        document.getElementById("nik").addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/\D/g, "");
        });
        
        function infoLayanan() {
            alert("Gunakan form di atas untuk mengajukan surat secara online dengan upload file pendukung.");
        }
        
        function scrollToForm() {
            document.getElementById("formPengajuan").scrollIntoView({ 
                behavior: "smooth", 
                block: "start" 
            });
        }
        
        function trackStatus() {
            const nomor = prompt("Masukkan nomor pengajuan Anda:");
            if (nomor) {
                window.location.href = "/tracking/" + nomor;
            }
        }
    </script>
</body>
</html>';
    }
    
    private function viewProfil($data)
    {
        echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Desa - Sistem Informasi Desa Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-profile { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 300px;
        }
        .profile-stats {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 15px;
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
        }
        .timeline-item {
            border-left: 3px solid #007bff;
            padding-left: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .timeline-item::before {
            content: "";
            position: absolute;
            left: -8px;
            top: 5px;
            width: 13px;
            height: 13px;
            background: #007bff;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-village me-2"></i>Desa Digital
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Beranda</a>
                <a class="nav-link" href="/layanan">Layanan</a>
                <a class="nav-link active" href="/profil">Profil Desa</a>
                <a class="nav-link" href="/login">Login Admin</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-profile text-white d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <h1 class="display-4 fw-bold mb-3">' . htmlspecialchars($data['nama_desa']) . '</h1>
                        <p class="lead mb-4">' . htmlspecialchars($data['kecamatan'] . ', ' . $data['kabupaten']) . '</p>
                        <p class="mb-4">
                            Desa yang kaya akan budaya dan tradisi, terletak di jantung Kalimantan Tengah. 
                            Dengan masyarakat yang ramah dan lingkungan yang asri, kami berkomitmen untuk 
                            memberikan pelayanan terbaik kepada seluruh warga.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="profile-stats p-4 text-center">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <h3 class="fw-bold">' . number_format($data['jumlah_penduduk']) . '</h3>
                                <small>Penduduk</small>
                            </div>
                            <div class="col-6 mb-3">
                                <h3 class="fw-bold">' . number_format($data['jumlah_kk']) . '</h3>
                                <small>KK</small>
                            </div>
                            <div class="col-6">
                                <h3 class="fw-bold">' . number_format($data['luas_wilayah'], 1) . '</h3>
                                <small>km²</small>
                            </div>
                            <div class="col-6">
                                <h3 class="fw-bold">' . number_format($data['jumlah_penduduk'] / $data['luas_wilayah'], 0) . '</h3>
                                <small>jiwa/km²</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Detail Profil -->
        <div class="row mb-5">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Umum</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><i class="fas fa-map-marker-alt me-2 text-primary"></i>Lokasi</h6>
                                <p class="text-muted">Jl. Trans Kalimantan KM 15<br>Kecamatan ' . htmlspecialchars($data['kecamatan']) . '<br>' . htmlspecialchars($data['kabupaten']) . ', Kalimantan Tengah</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-clock me-2 text-primary"></i>Jam Pelayanan</h6>
                                <p class="text-muted">Senin - Jumat: 08:00 - 16:00 WIB<br>Sabtu: 08:00 - 12:00 WIB<br>Minggu: Libur</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-phone me-2 text-primary"></i>Kontak</h6>
                                <p class="text-muted">Telepon: +62 813-5567-8901<br>Email: desa@tumbangjiutuh.go.id</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-calendar me-2 text-primary"></i>Didirikan</h6>
                                <p class="text-muted">15 Agustus 1945<br>Usia: 79 tahun</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visi Misi -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Visi & Misi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">VISI</h6>
                                <p class="text-muted">
                                    "Mewujudkan Desa Tumbang Jutuh yang Maju, Mandiri, dan Sejahtera 
                                    Berdasarkan Kearifan Lokal dan Teknologi Digital"
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">MISI</h6>
                                <ul class="text-muted">
                                    <li>Meningkatkan kualitas pelayanan publik</li>
                                    <li>Mengembangkan potensi ekonomi lokal</li>
                                    <li>Melestarikan budaya dan lingkungan</li>
                                    <li>Meningkatkan kualitas SDM</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sejarah -->
                <div class="card shadow">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Sejarah Singkat</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline-item">
                            <h6>1945 - Pembentukan Desa</h6>
                            <p class="text-muted small">Desa Tumbang Jutuh resmi dibentuk setelah kemerdekaan Indonesia sebagai bagian dari pengembangan wilayah Kalimantan Tengah.</p>
                        </div>
                        <div class="timeline-item">
                            <h6>1970 - Pembangunan Infrastruktur</h6>
                            <p class="text-muted small">Dimulainya pembangunan jalan Trans Kalimantan yang melewati desa, membuka akses transportasi dan ekonomi.</p>
                        </div>
                        <div class="timeline-item">
                            <h6>1995 - Modernisasi</h6>
                            <p class="text-muted small">Masuknya listrik dan telepon, serta pembangunan fasilitas pendidikan dan kesehatan modern.</p>
                        </div>
                        <div class="timeline-item">
                            <h6>2024 - Era Digital</h6>
                            <p class="text-muted small">Implementasi sistem informasi desa digital untuk meningkatkan pelayanan kepada masyarakat.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Pemerintahan -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Struktur Pemerintahan</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="bg-light rounded p-3 mb-2">
                                <i class="fas fa-user-tie fa-2x text-primary mb-2"></i>
                                <h6 class="mb-1">H. Ahmad Yani, S.Pd</h6>
                                <small class="text-muted">Kepala Desa</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-circle me-2 text-info"></i>
                                <div>
                                    <strong>Siti Aminah, A.Md</strong><br>
                                    <small class="text-muted">Sekretaris Desa</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-users me-2 text-secondary"></i>
                                <div>
                                    <strong>Budi Santoso</strong><br>
                                    <small class="text-muted">Kaur Umum</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calculator me-2 text-warning"></i>
                                <div>
                                    <strong>Rina Sari, S.E</strong><br>
                                    <small class="text-muted">Kaur Keuangan</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-leaf me-2 text-success"></i>
                                <div>
                                    <strong>Agus Setiawan</strong><br>
                                    <small class="text-muted">Kaur Pembangunan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Potensi Desa -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-leaf me-2"></i>Potensi Desa</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6><i class="fas fa-seedling me-2 text-success"></i>Pertanian</h6>
                            <p class="small text-muted">Padi, jagung, kacang tanah, dan sayuran organik. Lahan pertanian seluas 45 hektar.</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-fish me-2 text-info"></i>Perikanan</h6>
                            <p class="small text-muted">Budidaya ikan air tawar, lele, nila, dan patin. 15 kolam budidaya aktif.</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-hammer me-2 text-warning"></i>Kerajinan</h6>
                            <p class="small text-muted">Anyaman rotan, kerajinan kayu, dan produk kerajinan tangan khas Dayak.</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-mountain me-2 text-danger"></i>Wisata</h6>
                            <p class="small text-muted">Wisata alam, budaya Dayak, dan ekowisata sungai. Potensi pengembangan besar.</p>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas -->
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Fasilitas Umum</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <i class="fas fa-school fa-2x text-primary mb-2"></i>
                                <div><strong>2</strong></div>
                                <small class="text-muted">Sekolah</small>
                            </div>
                            <div class="col-6 mb-3">
                                <i class="fas fa-hospital fa-2x text-success mb-2"></i>
                                <div><strong>1</strong></div>
                                <small class="text-muted">Puskesmas</small>
                            </div>
                            <div class="col-6 mb-3">
                                <i class="fas fa-mosque fa-2x text-info mb-2"></i>
                                <div><strong>3</strong></div>
                                <small class="text-muted">Masjid</small>
                            </div>
                            <div class="col-6 mb-3">
                                <i class="fas fa-store fa-2x text-warning mb-2"></i>
                                <div><strong>8</strong></div>
                                <small class="text-muted">Toko</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2025 Sistem Informasi Desa Digital - Kabupaten Katingan</p>
            <p class="text-muted">
                <i class="fas fa-phone me-2"></i>+62 813-5567-8901 | 
                <i class="fas fa-envelope me-2"></i>desa@tumbangjiutuh.go.id
            </p>
            <p class="text-muted small mt-2">
                <i class="fas fa-code me-1"></i>Dibuat oleh Muhammad Hadianur Al Rafi
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
    }
    
    private function viewTracking($tracking_number)
    {
        // Get tracking information from database
        $tracking_info = $this->getTrackingInfo($tracking_number);
        
        echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Status - Sistem Informasi Desa Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: "";
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: "";
            position: absolute;
            left: -22px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #6c757d;
        }
        .timeline-item.active::before {
            border-color: #28a745;
            background: #28a745;
        }
        .timeline-item.current::before {
            border-color: #007bff;
            background: #007bff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-home me-2"></i>Portal Desa Digital
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">Beranda</a>
                <a class="nav-link" href="/layanan">Layanan</a>
                <a class="nav-link" href="/profil">Profil Desa</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                        <li class="breadcrumb-item active">Tracking Status</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-search me-2"></i>Status Pengajuan Surat
                        </h4>
                    </div>
                    <div class="card-body">';
        
        if ($tracking_info) {
            $status_class = match($tracking_info['status']) {
                'pending' => 'warning',
                'diproses' => 'info', 
                'selesai' => 'success',
                'ditolak' => 'danger',
                default => 'secondary'
            };
            
            $status_text = match($tracking_info['status']) {
                'pending' => 'Menunggu Verifikasi',
                'diproses' => 'Sedang Diproses',
                'selesai' => 'Selesai',
                'ditolak' => 'Ditolak',
                default => 'Status Tidak Diketahui'
            };
            
            echo '
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Nomor Pengajuan:</strong> ' . htmlspecialchars($tracking_number) . '
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><i class="fas fa-file-alt me-2"></i>Jenis Surat</h6>
                                <p class="text-muted">' . htmlspecialchars($tracking_info['jenis_surat']) . '</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-calendar me-2"></i>Tanggal Pengajuan</h6>
                                <p class="text-muted">' . date('d/m/Y H:i', strtotime($tracking_info['tanggal_pengajuan'])) . '</p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><i class="fas fa-user me-2"></i>Nama Pemohon</h6>
                                <p class="text-muted">' . htmlspecialchars($tracking_info['nama_pemohon']) . '</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-flag me-2"></i>Status Saat Ini</h6>
                                <span class="badge bg-' . $status_class . ' fs-6">' . $status_text . '</span>
                            </div>
                        </div>
                        
                        <h6><i class="fas fa-clock me-2"></i>Timeline Proses</h6>
                        <div class="timeline">
                            <div class="timeline-item active">
                                <strong>Pengajuan Diterima</strong>
                                <div class="text-muted small">' . date('d/m/Y H:i', strtotime($tracking_info['tanggal_pengajuan'])) . '</div>
                                <p class="small">Pengajuan surat telah diterima dan terdaftar dalam sistem.</p>
                            </div>';
                            
            if ($tracking_info['status'] !== 'pending') {
                echo '
                            <div class="timeline-item active">
                                <strong>Verifikasi Berkas</strong>
                                <div class="text-muted small">' . date('d/m/Y H:i', strtotime($tracking_info['tanggal_pengajuan'] . ' +1 hour')) . '</div>
                                <p class="small">Berkas pengajuan telah diverifikasi dan memenuhi persyaratan.</p>
                            </div>';
            }
            
            if ($tracking_info['status'] === 'diproses') {
                echo '
                            <div class="timeline-item current">
                                <strong>Sedang Diproses</strong>
                                <div class="text-muted small">Saat ini</div>
                                <p class="small">Surat sedang dalam proses pembuatan dan penandatanganan.</p>
                            </div>';
            } elseif ($tracking_info['status'] === 'selesai') {
                echo '
                            <div class="timeline-item active">
                                <strong>Sedang Diproses</strong>
                                <div class="text-muted small">' . date('d/m/Y H:i', strtotime($tracking_info['tanggal_pengajuan'] . ' +2 hours')) . '</div>
                                <p class="small">Surat telah dalam proses pembuatan dan penandatanganan.</p>
                            </div>
                            <div class="timeline-item active">
                                <strong>Selesai</strong>
                                <div class="text-muted small">' . date('d/m/Y H:i', strtotime($tracking_info['tanggal_selesai'] ?? $tracking_info['tanggal_pengajuan'] . ' +1 day')) . '</div>
                                <p class="small">Surat telah selesai dan siap untuk diambil.</p>
                            </div>';
            } elseif ($tracking_info['status'] === 'ditolak') {
                echo '
                            <div class="timeline-item" style="color: #dc3545;">
                                <strong>Ditolak</strong>
                                <div class="text-muted small">' . date('d/m/Y H:i', strtotime($tracking_info['tanggal_pengajuan'] . ' +2 hours')) . '</div>
                                <p class="small">Pengajuan ditolak. ' . htmlspecialchars($tracking_info['keterangan'] ?? 'Silakan hubungi kantor desa untuk informasi lebih lanjut.') . '</p>
                            </div>';
            }
            
            echo '
                        </div>';
                        
            if ($tracking_info['status'] === 'selesai') {
                echo '
                        <div class="alert alert-success mt-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Surat Anda telah selesai!</strong><br>
                            Silakan datang ke kantor desa untuk mengambil surat dengan membawa:
                            <ul class="mt-2 mb-0">
                                <li>Kartu identitas (KTP/KK)</li>
                                <li>Nomor pengajuan: ' . htmlspecialchars($tracking_number) . '</li>
                            </ul>
                        </div>';
            } elseif ($tracking_info['status'] === 'ditolak') {
                echo '
                        <div class="alert alert-danger mt-4">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Pengajuan Ditolak</strong><br>
                            Untuk informasi lebih lanjut, silakan hubungi kantor desa di nomor telepon +62 XXX-XXXX-XXXX.
                        </div>';
            }
        } else {
            echo '
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>Nomor pengajuan tidak ditemukan!</strong><br>
                                Pastikan nomor pengajuan yang Anda masukkan benar.
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">Nomor pengajuan: <strong>' . htmlspecialchars($tracking_number) . '</strong></p>
                            <p>Jika Anda yakin nomor pengajuan benar, silakan hubungi kantor desa.</p>
                        </div>';
        }
        
        echo '
                        <div class="text-center mt-4">
                            <a href="/" class="btn btn-primary me-2">
                                <i class="fas fa-home me-1"></i>Kembali ke Beranda
                            </a>
                            <button class="btn btn-outline-primary" onclick="searchAgain()">
                                <i class="fas fa-search me-1"></i>Cari Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2025 Sistem Informasi Desa Digital - Kabupaten Katingan</p>
            <p class="text-muted">
                <i class="fas fa-phone me-2"></i>+62 XXX-XXXX-XXXX | 
                <i class="fas fa-envelope me-2"></i>info@desadigital.com
            </p>
            <p class="text-muted small mt-2">
                <i class="fas fa-code me-1"></i>Dibuat oleh Muhammad Hadianur Al Rafi
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchAgain() {
            const nomor = prompt("Masukkan nomor pengajuan Anda:");
            if (nomor) {
                window.location.href = "/tracking/" + nomor;
            }
        }
    </script>
</body>
</html>';
    }
    
    private function getTrackingInfo($tracking_number)
    {
        try {
            // Include database helper
            require_once dirname(__DIR__) . '/Helpers/DatabaseHelper.php';
            $database = new \App\Helpers\DatabaseHelper();
            
            // Try to get from database first
            $result = $database->getPengajuan($tracking_number);
            
            if ($result) {
                return $result;
            } else {
                // Fallback to sample data for demo
                return $this->getSampleTrackingData($tracking_number);
            }
        } catch (\Exception $e) {
            error_log("Tracking info error: " . $e->getMessage());
            // Return sample data for demo
            return $this->getSampleTrackingData($tracking_number);
        }
    }
    
    private function getSampleTrackingData($tracking_number)
    {
        // Sample tracking data for demonstration
        $sample_data = [
            'SK001-2024' => [
                'nomor_pengajuan' => 'SK001-2024',
                'jenis_surat' => 'Surat Keterangan Domisili',
                'nama_pemohon' => 'Ahmad Fadli',
                'tanggal_pengajuan' => '2024-07-10 09:30:00',
                'status' => 'selesai',
                'tanggal_selesai' => '2024-07-11 14:00:00',
                'keterangan' => ''
            ],
            'SK002-2024' => [
                'nomor_pengajuan' => 'SK002-2024',
                'jenis_surat' => 'Surat Keterangan Usaha',
                'nama_pemohon' => 'Siti Nurhaliza',
                'tanggal_pengajuan' => '2024-07-11 10:15:00',
                'status' => 'diproses',
                'tanggal_selesai' => null,
                'keterangan' => ''
            ],
            'SK003-2024' => [
                'nomor_pengajuan' => 'SK003-2024',
                'jenis_surat' => 'Surat Keterangan Tidak Mampu',
                'nama_pemohon' => 'Budi Santoso',
                'tanggal_pengajuan' => '2024-07-11 11:00:00',
                'status' => 'pending',
                'tanggal_selesai' => null,
                'keterangan' => ''
            ],
            'SK004-2025' => [
                'nomor_pengajuan' => 'SK004-2025',
                'jenis_surat' => 'Surat Keterangan Domisili',
                'nama_pemohon' => 'Rina Sari',
                'tanggal_pengajuan' => '2025-07-11 14:30:00',
                'status' => 'pending',
                'tanggal_selesai' => null,
                'keterangan' => 'Pengajuan melalui sistem online'
            ]
        ];
        
        return $sample_data[$tracking_number] ?? null;
    }
    
    public function submitPengajuan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        try {
            // Include helper classes
            require_once dirname(__DIR__) . '/Helpers/GoogleDriveHelper.php';
            require_once dirname(__DIR__) . '/Helpers/DatabaseHelper.php';
            
            // Validate required fields
            $requiredFields = ['nama', 'nik', 'jenis_surat', 'keperluan', 'alamat'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new \Exception("Field $field wajib diisi");
                }
            }
            
            // Validate NIK (16 digits)
            if (!preg_match('/^\d{16}$/', $_POST['nik'])) {
                throw new \Exception("NIK harus terdiri dari 16 digit angka");
            }
            
            // Handle file upload
            if (!isset($_FILES['file_pendukung']) || $_FILES['file_pendukung']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception("File pendukung wajib diupload");
            }
            
            $file = $_FILES['file_pendukung'];
            
            // Validate file type using multiple methods
            $fileName = $file['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $mimeType = $file['type'];
            
            // Check file extension and MIME type
            if ($fileExtension !== 'pdf' || !in_array($mimeType, ['application/pdf', 'application/x-pdf'])) {
                throw new \Exception("File harus berformat PDF");
            }
            
            // Additional validation: check file header for PDF signature
            $fileHandle = fopen($file['tmp_name'], 'rb');
            $fileHeader = fread($fileHandle, 4);
            fclose($fileHandle);
            
            if ($fileHeader !== '%PDF') {
                throw new \Exception("File yang diupload bukan file PDF yang valid");
            }
            
            // Validate file size (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new \Exception("Ukuran file maksimal 5MB");
            }
            
            // Generate tracking number
            $trackingNumber = 'SK' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) . '-' . date('Y');
            
            // Create upload directory if not exists
            $uploadDir = 'public/uploads/pendukung/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $fileName = $trackingNumber . '_' . time() . '.pdf';
            $filePath = $uploadDir . $fileName;
            
            // Move uploaded file to local storage
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new \Exception("Gagal menyimpan file ke storage lokal");
            }
            
            // Initialize helpers
            $googleDrive = new \App\Helpers\GoogleDriveHelper();
            $database = new \App\Helpers\DatabaseHelper();
            
            // Upload file to Google Drive
            $driveUpload = $googleDrive->uploadFile($filePath, $fileName, 'application/pdf');
            
            $googleDriveId = null;
            $googleDriveLink = null;
            
            if ($driveUpload['success']) {
                $googleDriveId = $driveUpload['file_id'];
                $googleDriveLink = $driveUpload['web_view_link'];
                
                // Share the file (make it accessible)
                $shareResult = $googleDrive->shareFile($googleDriveId);
            } else {
                // Log the error but don't fail the submission
                error_log("Google Drive upload failed: " . $driveUpload['error']);
            }
            
            // Prepare data for database
            $pengajuan = [
                'nomor_pengajuan' => $trackingNumber,
                'nama_pemohon' => $_POST['nama'],
                'nik' => $_POST['nik'],
                'jenis_surat' => $_POST['jenis_surat'],
                'keperluan' => $_POST['keperluan'],
                'alamat' => $_POST['alamat'],
                'catatan' => $_POST['catatan'] ?? '',
                'file_pendukung' => $fileName,
                'google_drive_id' => $googleDriveId,
                'google_drive_link' => $googleDriveLink,
                'tanggal_pengajuan' => date('Y-m-d H:i:s'),
                'status' => 'pending'
            ];
            
            // Save to database
            $saveResult = $database->savePengajuan($pengajuan);
            
            if (!$saveResult['success']) {
                // Log error but don't fail the submission entirely
                error_log("Database save failed: " . $saveResult['error']);
            }
            
            // Log upload details
            $database->logUpload([
                'nomor_pengajuan' => $trackingNumber,
                'filename' => $fileName,
                'file_size' => $file['size'],
                'file_type' => 'application/pdf',
                'local_path' => $filePath,
                'google_drive_id' => $googleDriveId,
                'google_drive_status' => $driveUpload['success'] ? 'uploaded' : 'failed'
            ]);
            
            // Prepare response
            $response = [
                'success' => true,
                'tracking_number' => $trackingNumber,
                'message' => 'Pengajuan berhasil dikirim dan disimpan',
                'details' => [
                    'file_saved_locally' => true,
                    'google_drive_upload' => $driveUpload['success'],
                    'database_saved' => $saveResult['success'],
                    'file_size' => number_format($file['size'] / 1024, 2) . ' KB'
                ]
            ];
            
            // Include Google Drive link if upload was successful
            if ($driveUpload['success']) {
                $response['google_drive_link'] = $googleDriveLink;
                $response['google_drive_id'] = $googleDriveId;
            }
            
            // Return success response
            echo json_encode($response);
            
        } catch (\Exception $e) {
            // Clean up uploaded file if it exists
            if (isset($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }
            
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}