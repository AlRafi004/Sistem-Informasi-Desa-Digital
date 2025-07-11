<?php

// Simple entry point for Sistem Informasi Desa Digital

// Simple autoloader for our application
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load configuration
$config = [
    'database' => [
        'host' => 'localhost',
        'database' => 'desa_digital',
        'username' => 'root',
        'password' => ''
    ]
];

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    if ($env) {
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Start session
session_start();

// Simple router
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove trailing slash
$request_uri = rtrim($request_uri, '/');
if (empty($request_uri)) {
    $request_uri = '/';
}

// Basic routing
try {
    switch ($request_uri) {
        case '/':
            $controller = new \App\Controllers\PublicController();
            $controller->index();
            break;
            
        case '/login':
            if ($request_method === 'GET') {
                $controller = new \App\Controllers\AuthController();
                $controller->showLogin();
            } elseif ($request_method === 'POST') {
                $controller = new \App\Controllers\AuthController();
                $controller->handleLogin();
            }
            break;
            
        case '/logout':
            \App\Controllers\AuthController::logout();
            header('Location: /');
            exit;
            break;
            
        case '/dashboard':
            if (!\App\Controllers\AuthController::check()) {
                header('Location: /login');
                exit;
            }
            $controller = new \App\Controllers\DashboardController();
            $controller->index();
            break;
            
        case '/layanan':
            if ($request_method === 'GET') {
                $controller = new \App\Controllers\PublicController();
                $controller->layanan();
            } elseif ($request_method === 'POST') {
                $controller = new \App\Controllers\PublicController();
                $controller->submitPengajuan();
            }
            break;
        
        case '/submit-pengajuan':
            if ($request_method === 'POST') {
                $controller = new \App\Controllers\PublicController();
                $controller->submitPengajuan();
            } else {
                header('Location: /layanan');
                exit;
            }
            break;
            
        case '/profil':
            $controller = new \App\Controllers\PublicController();
            $controller->profil();
            break;
            
        case '/penduduk':
            if (!\App\Controllers\AuthController::check()) {
                header('Location: /login');
                exit;
            }
            if ($request_method === 'GET') {
                $controller = new \App\Controllers\PendudukController();
                $controller->index();
            } elseif ($request_method === 'POST') {
                $controller = new \App\Controllers\PendudukController();
                $controller->store();
            }
            break;
            
        case '/penduduk/create':
            if (!\App\Controllers\AuthController::check()) {
                header('Location: /login');
                exit;
            }
            $controller = new \App\Controllers\PendudukController();
            $controller->create();
            break;
            
        case '/layanan-surat':
            if (!\App\Controllers\AuthController::check()) {
                header('Location: /login');
                exit;
            }
            $controller = new \App\Controllers\LayananSuratController();
            $controller->index();
            break;
            
        case '/profil-desa':
            if (!\App\Controllers\AuthController::check()) {
                header('Location: /login');
                exit;
            }
            $controller = new \App\Controllers\ProfilDesaController();
            $controller->index();
            break;
            
        case '/admin':
            $controller = new \App\Controllers\AdminController();
            $controller->dashboard();
            break;
            
        case '/admin/pengajuan':
            $controller = new \App\Controllers\AdminController();
            $controller->pengajuan();
            break;
            
        default:
            // Handle tracking routes
            if (preg_match('/^\/tracking\/(.+)$/', $request_uri, $matches)) {
                $tracking_number = $matches[1];
                $controller = new \App\Controllers\PublicController();
                $controller->tracking($tracking_number);
                break;
            }
            // 404 page
            http_response_code(404);
            echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-6 text-center'>
                <h1 class='display-1'>404</h1>
                <h2>Halaman Tidak Ditemukan</h2>
                <p class='text-muted'>Halaman yang Anda cari tidak tersedia.</p>
                <a href='/' class='btn btn-primary'>Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>";
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>500 - Server Error</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-8'>
                <div class='alert alert-danger'>
                    <h4>Server Error</h4>
                    <p>" . htmlspecialchars($e->getMessage()) . "</p>
                    <small class='text-muted'>File: " . htmlspecialchars($e->getFile()) . " Line: " . $e->getLine() . "</small>
                </div>
                <a href='/' class='btn btn-primary'>Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>";
}
