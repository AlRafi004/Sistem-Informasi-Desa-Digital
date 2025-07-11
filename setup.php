<?php
/**
 * Setup Script for Sistem Informasi Desa Digital
 * Run this script to initialize the application
 */

echo "=== Sistem Informasi Desa Digital Setup ===\n\n";

// Check PHP version
if (version_compare(PHP_VERSION, '8.1.0') < 0) {
    echo "❌ PHP 8.1 or higher is required. Current version: " . PHP_VERSION . "\n";
    exit(1);
}
echo "✅ PHP version: " . PHP_VERSION . "\n";

// Check if .env file exists
if (!file_exists('.env')) {
    if (file_exists('.env.example')) {
        copy('.env.example', '.env');
        echo "✅ Created .env file from .env.example\n";
    } else {
        echo "❌ .env.example file not found\n";
        exit(1);
    }
} else {
    echo "✅ .env file exists\n";
}

// Check required directories
$directories = [
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache',
    'public/uploads',
    'public/uploads/pendukung',
    'public/uploads/surat'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Created directory: $dir\n";
    }
}

// Set proper permissions
if (PHP_OS_FAMILY !== 'Windows') {
    chmod('storage', 0755);
    chmod('bootstrap/cache', 0755);
    echo "✅ Set directory permissions\n";
}

// Generate app key if not exists
$envContent = file_get_contents('.env');
if (strpos($envContent, 'APP_KEY=base64:') === false) {
    $key = 'base64:' . base64_encode(random_bytes(32));
    $envContent = str_replace('APP_KEY=base64:YourGeneratedAppKeyHere', "APP_KEY=$key", $envContent);
    file_put_contents('.env', $envContent);
    echo "✅ Generated application key\n";
}

echo "\n=== Setup Complete! ===\n\n";
echo "Next steps:\n";
echo "1. Install Composer dependencies:\n";
echo "   composer install\n\n";
echo "2. Set up your database:\n";
echo "   - Create MySQL database 'desa_digital'\n";
echo "   - Import database/desa_digital.sql\n";
echo "   - Update .env database settings\n\n";
echo "3. Start the development server:\n";
echo "   php -S localhost:8000 -t public\n\n";
echo "4. Access the application:\n";
echo "   - Public site: http://localhost:8000\n";
echo "   - Admin login: http://localhost:8000/login\n\n";
echo "Default admin accounts:\n";
echo "   Kepala Desa: kepala@desa.com / password\n";
echo "   Sekretaris: sekretaris@desa.com / password\n";
echo "   Kaur: kaur@desa.com / password\n\n";
?>
