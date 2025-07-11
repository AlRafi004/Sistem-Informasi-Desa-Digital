<?php

namespace App\Controllers;

use PDO;
use PDOException;
use App\Models\User;

class AuthController
{
    public function handleLogin()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (self::attemptLogin($email, $password)) {
            header('Location: /dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Email atau password tidak valid.';
            header('Location: /login');
            exit;
        }
    }
    
    public static function attemptLogin($email, $password)
    {
        // Use User model for authentication
        $user = User::findByEmail($email);
        
        if ($user && $user->verifyPassword($password)) {
            $userData = $user->toArray();
            $_SESSION['user'] = $userData;
            
            // Also set individual session variables for compatibility
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_name'] = $userData['name'];
            $_SESSION['user_role'] = $userData['role'];
            $_SESSION['user_email'] = $userData['email'];
            
            return true;
        }
        
        // Fallback to demo credentials
        return self::validateDemoCredentials($email, $password);
    }
    
    private static function validateDemoCredentials($email, $password)
    {
        $demoUsers = [
            'kepala@desa.com' => ['password' => 'password', 'id' => 1, 'role' => 'kepala_desa', 'name' => 'Kepala Desa'],
            'sekretaris@desa.com' => ['password' => 'password', 'id' => 2, 'role' => 'sekretaris', 'name' => 'Sekretaris Desa'],
            'kaur@desa.com' => ['password' => 'password', 'id' => 3, 'role' => 'kaur', 'name' => 'Kaur Pemerintahan']
        ];
        
        if (isset($demoUsers[$email]) && $demoUsers[$email]['password'] === $password) {
            $_SESSION['user'] = $demoUsers[$email];
            $_SESSION['user']['email'] = $email;
            
            // Also set individual session variables for compatibility
            $_SESSION['user_id'] = $demoUsers[$email]['id'];
            $_SESSION['user_name'] = $demoUsers[$email]['name'];
            $_SESSION['user_role'] = $demoUsers[$email]['role'];
            $_SESSION['user_email'] = $email;
            
            return true;
        }
        return false;
    }
    
    public static function check()
    {
        return isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user_id']);
    }
    
    public static function user()
    {
        if (!self::check()) {
            return null;
        }
        
        // Return user data from session (already formatted)
        return $_SESSION['user'] ?? null;
    }
    
    public static function logout()
    {
        session_destroy();
    }
    
    private static function getDatabase()
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
    
    public function showLogin()
    {
        if (self::check()) {
            header('Location: /dashboard');
            exit;
        }
        
        $this->view('auth/login');
    }
    
    private function view($template, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../../resources/views/' . $template . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Simple login form
            echo $this->getSimpleLoginForm();
        }
    }
    
    private function getSimpleLoginForm()
    {
        $error = $_SESSION['error'] ?? '';
        unset($_SESSION['error']);
        
        return "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Login - Sistem Informasi Desa Digital</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>
    <div class='container'>
        <div class='row justify-content-center mt-5'>
            <div class='col-md-6 col-lg-4'>
                <div class='card shadow'>
                    <div class='card-body p-5'>
                        <div class='text-center mb-4'>
                            <i class='fas fa-home fa-3x text-primary mb-3'></i>
                            <h3 class='fw-bold'>Desa Digital</h3>
                            <p class='text-muted'>Sistem Informasi Desa</p>
                        </div>
                        
                        " . ($error ? "<div class='alert alert-danger'>$error</div>" : "") . "
                        
                        <form method='POST' action='/login'>
                            <div class='mb-3'>
                                <label for='email' class='form-label'>Email</label>
                                <div class='input-group'>
                                    <span class='input-group-text'><i class='fas fa-envelope'></i></span>
                                    <input type='email' class='form-control' id='email' name='email' required autofocus>
                                </div>
                            </div>
                            
                            <div class='mb-3'>
                                <label for='password' class='form-label'>Password</label>
                                <div class='input-group'>
                                    <span class='input-group-text'><i class='fas fa-lock'></i></span>
                                    <input type='password' class='form-control' id='password' name='password' required>
                                </div>
                            </div>
                            
                            <div class='d-grid'>
                                <button type='submit' class='btn btn-primary btn-lg'>
                                    <i class='fas fa-sign-in-alt me-2'></i>Login
                                </button>
                            </div>
                        </form>
                        
                        <div class='text-center mt-4'>
                            <small class='text-muted'>
                                <a href='/' class='text-decoration-none'>
                                    <i class='fas fa-arrow-left me-1'></i>Kembali ke Beranda
                                </a>
                            </small>
                        </div>
                        
                        <hr class='my-4'>
                        <div class='text-center'>
                            <h6 class='text-muted mb-3'>Akun Demo:</h6>
                            <small><strong>Kepala Desa:</strong> kepala@desa.com / password</small><br>
                            <small><strong>Sekretaris:</strong> sekretaris@desa.com / password</small><br>
                            <small><strong>Kaur:</strong> kaur@desa.com / password</small>
                        </div>
                    </div>
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
</body>
</html>";
    }
}
