<?php

namespace App\Helpers;

/*
|--------------------------------------------------------------------------
| Configuration Helper
|--------------------------------------------------------------------------
|
| This helper provides easy access to application configuration
| for the custom Sistem Informasi Desa Digital application.
|
*/

class ConfigHelper
{
    private static $config = null;
    
    /**
     * Load configuration from file
     */
    private static function loadConfig()
    {
        if (self::$config === null) {
            $configPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
            self::$config = require $configPath;
        }
        return self::$config;
    }
    
    /**
     * Get configuration value using dot notation
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $config = self::loadConfig();
        
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
    
    /**
     * Get all configuration
     * 
     * @return array
     */
    public static function all()
    {
        return self::loadConfig();
    }
    
    /**
     * Get app name
     * 
     * @return string
     */
    public static function appName()
    {
        return self::get('name', 'Sistem Informasi Desa Digital');
    }
    
    /**
     * Get app URL
     * 
     * @return string
     */
    public static function appUrl()
    {
        return self::get('url', 'http://localhost:8000');
    }
    
    /**
     * Get database configuration
     * 
     * @return array
     */
    public static function database()
    {
        return self::get('database', [
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'desa_digital',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);
    }
    
    /**
     * Get village information
     * 
     * @return array
     */
    public static function village()
    {
        return self::get('village', [
            'name' => 'Desa Tumbang Jutuh',
            'code' => '62.01.01.2001',
            'district' => 'Katingan Hulu',
            'regency' => 'Kabupaten Katingan',
            'province' => 'Kalimantan Tengah',
            'postal_code' => '74411',
        ]);
    }
    
    /**
     * Check if feature is enabled
     * 
     * @param string $feature
     * @return bool
     */
    public static function isFeatureEnabled($feature)
    {
        $features = self::get('features', []);
        return isset($features[$feature]) && $features[$feature] === true;
    }
    
    /**
     * Get user roles
     * 
     * @return array
     */
    public static function userRoles()
    {
        return self::get('features.user_roles', ['kepala_desa', 'sekretaris', 'kaur']);
    }
    
    /**
     * Get upload configuration
     * 
     * @return array
     */
    public static function upload()
    {
        return self::get('upload', [
            'max_file_size' => 2048,
            'allowed_extensions' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
            'upload_path' => 'public/uploads/',
            'temp_path' => 'storage/temp/',
        ]);
    }
    
    /**
     * Get pagination settings
     * 
     * @return array
     */
    public static function pagination()
    {
        return self::get('pagination', [
            'per_page' => 10,
            'max_per_page' => 100,
        ]);
    }
    
    /**
     * Check if debug mode is enabled
     * 
     * @return bool
     */
    public static function isDebug()
    {
        return self::get('debug', false);
    }
    
    /**
     * Get timezone
     * 
     * @return string
     */
    public static function timezone()
    {
        return self::get('timezone', 'Asia/Jakarta');
    }
    
    /**
     * Get date format
     * 
     * @param string $type
     * @return string
     */
    public static function dateFormat($type = 'date')
    {
        $formats = self::get('formats', [
            'date' => 'd/m/Y',
            'datetime' => 'd/m/Y H:i:s',
            'time' => 'H:i:s',
        ]);
        
        return $formats[$type] ?? $formats['date'];
    }
    
    /**
     * Get application path
     * 
     * @param string $path
     * @return string
     */
    public static function path($path = '')
    {
        $paths = self::get('paths', []);
        $basePath = $paths['root'] ?? dirname(__DIR__, 2);
        
        if (empty($path)) {
            return $basePath;
        }
        
        if (isset($paths[$path])) {
            return $paths[$path];
        }
        
        return $basePath . '/' . ltrim($path, '/');
    }
}
