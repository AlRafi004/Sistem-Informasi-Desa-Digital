<h1 align="center">Sistem Informasi Desa Digital</h1>

<p align="center"><strong>Sistem Informasi Desa Digital untuk Kabupaten Katingan, Kalimantan Tengah</strong></p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap-5.3.0-7952B3?style=flat&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

---

## 📋 Deskripsi

**Sistem Informasi Desa Digital** adalah platform web berbasis PHP yang dikembangkan untuk memudahkan administrasi dan pelayanan publik di desa-desa Kabupaten Katingan. Sistem ini menyediakan interface yang user-friendly untuk pengelolaan data penduduk, pengajuan surat-menyurat, dan informasi profil desa.

---

## ✨ Fitur Utama

### 🔐 Sistem Autentikasi Multi-Role
- **Kepala Desa**: Akses penuh ke semua fitur administratif  
- **Sekretaris**: Mengelola dokumen dan administrasi  
- **Kaur**: Menangani urusan pemerintahan dan pelayanan

### 📄 Layanan Surat Online
- Pengajuan surat keterangan domisili  
- Pengajuan surat keterangan usaha  
- Pengajuan surat keterangan umum  
- Pengajuan surat keterangan catatan kepolisian (SKCK)  
- Tracking status pengajuan real-time  
- Upload file pendukung dengan integrasi Google Drive

### 🏛️ Manajemen Profil Desa
- Informasi geografis dan demografis  
- Data batas wilayah  
- Visi, misi, dan sejarah desa  
- Potensi wisata dan UMKM

### 👥 Pengelolaan Data Penduduk
- Database penduduk terintegrasi  
- Informasi KTP dan KK  
- Status keluarga dan kependudukan

### 📊 Dashboard Admin
- Statistik pengajuan surat  
- Monitoring aktivitas sistem  
- Laporan dan analytics

---

## 🚀 Teknologi yang Digunakan

- **Backend**: PHP 8.1+ dengan arsitektur MVC custom  
- **Database**: MySQL 5.7+ dengan fallback JSON storage  
- **Frontend**: Bootstrap 5.3.0 + Font Awesome 6.4.0  
- **File Storage**: Local storage + Google Drive API integration  
- **Authentication**: Session-based dengan password hashing

---

## 🛠️ Instalasi

### Persyaratan Sistem
- PHP 8.1 atau lebih tinggi  
- MySQL 5.7 atau lebih tinggi  
- Web server (Apache/Nginx)  
- Composer (opsional)

### Langkah Instalasi

1. **Clone repository**
    ```bash
    git clone https://github.com/AlRafi004/Sistem-Informasi-Desa-Digital.git
    cd Sistem-Informasi-Desa-Digital
    ```

2. **Setup database**
    ```bash
    # Import database schema
    mysql -u root -p < database/desa_digital.sql
    ```

3. **Konfigurasi environment**
    ```bash
    # Copy file konfigurasi
    cp .env.example .env

    # Edit konfigurasi database di .env
    nano .env
    ```

4. **Jalankan aplikasi**
    ```bash
    # Menggunakan PHP built-in server
    php -S localhost:8000 -t public
    ```

5. **Akses aplikasi**
    - URL: `http://localhost:8000`  
    - Admin Dashboard: `http://localhost:8000/admin`

---

## 📁 Struktur Proyek

```
Sistem-Informasi-Desa-Digital/
├── app/
│ ├── Controllers/ # Controller classes
│ ├── Models/ # Model classes
│ └── Helpers/ # Helper utilities
├── config/ # Configuration files
├── database/ # Database schema and migrations
├── public/ # Public web files
│ ├── index.php # Entry point
│ └── uploads/ # File uploads
├── storage/ # Storage and logs
├── .env.example # Environment template
└── README.md # Dokumentasi proyek
```


---

## 🔧 Konfigurasi

### Database (`.env`)
```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=desa_digital
DB_USERNAME=root
DB_PASSWORD=
```

## 📁 File Upload

- **Max file size**: 5MB  
- **Allowed types**: PDF, JPG, PNG, DOCX  
- **Storage**: Local + Google Drive backup

---

## 📱 Responsive Design

Dirancang dengan pendekatan **mobile-first** menggunakan Bootstrap 5 untuk mendukung berbagai perangkat:

- 📱 Mobile phones  
- 💻 Desktop computers  
- 📟 Tablets

---

## 🔒 Keamanan

- Password hashing dengan **bcrypt**  
- **CSRF protection**  
- **Input validation** dan **sanitization**  
- **Session management** aman  
- **File upload validation**

---

## 📈 Roadmap

- [ ] API REST untuk integrasi mobile app  
- [ ] Sistem notifikasi email/SMS  
- [ ] Laporan dan dashboard analytics  
- [ ] Integrasi dengan sistem keuangan desa  
- [ ] Multi-bahasa (Bahasa Indonesia/Dayak)

---

## 🤝 Kontribusi

1. **Fork** repository ini  
2. Buat branch fitur baru:
    ```bash
    git checkout -b feature/NamaFitur
    ```
3. Commit perubahan:
    ```bash
    git commit -m "Menambahkan fitur baru"
    ```
4. Push ke branch:
    ```bash
    git push origin feature/NamaFitur
    ```
5. Buat **Pull Request**

---

**© 2025 Sistem Informasi Desa Digital - Kabupaten Katingan**  
_Dibuat oleh Muhammad Hadianur Al Rafi_
