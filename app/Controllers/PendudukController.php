<?php

namespace App\Controllers;

class PendudukController
{
    public function index()
    {
        if (!$this->checkAuth()) {
            header('Location: /login');
            exit;
        }
        
        $this->view('penduduk/index');
    }
    
    public function create()
    {
        if (!$this->checkAuth()) {
            header('Location: /login');
            exit;
        }
        
        $this->view('penduduk/create');
    }
    
    public function store()
    {
        if (!$this->checkAuth()) {
            header('Location: /login');
            exit;
        }
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Basic validation and processing would go here
            header('Location: /penduduk');
            exit;
        }
    }
    
    private function checkAuth()
    {
        return isset($_SESSION['user_id']);
    }
    
    private function view($template, $data = [])
    {
        echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penduduk - Sistem Informasi Desa Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">
                <i class="fas fa-home me-2"></i>Dashboard Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/dashboard">Dashboard</a>
                <a class="nav-link active" href="/penduduk">Data Penduduk</a>
                <a class="nav-link" href="/logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Data Penduduk</h2>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Fitur manajemen data penduduk sedang dalam pengembangan.
        </div>
        <a href="/dashboard" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-3 mt-5">
        <div class="container">
            <p>&copy; 2025 Sistem Informasi Desa Digital - Kabupaten Katingan</p>
            <p class="small">Dibuat oleh Muhammad Hadianur Al Rafi</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
    }
}
