<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => 'Sistem Informasi Desa Digital',

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes.
    |
    */

    'env' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => true,

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => 'http://localhost:8000',

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Jakarta',

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Database connection information for the village information system.
    |
    */

    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'desa_digital',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    /*
    |--------------------------------------------------------------------------
    | Village Information
    |--------------------------------------------------------------------------
    |
    | Configuration for the village that this system serves.
    |
    */

    'village' => [
        'name' => 'Desa Tumbang Jutuh',
        'code' => '62.01.01.2001',
        'district' => 'Katingan Hulu',
        'regency' => 'Kabupaten Katingan',
        'province' => 'Kalimantan Tengah',
        'postal_code' => '74411',
        'head_of_village' => '',
        'village_secretary' => '',
        'address' => '',
        'phone' => '',
        'email' => '',
        'website' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features of the application.
    |
    */

    'features' => [
        'user_management' => true,
        'resident_data' => true,
        'letter_services' => true,
        'village_profile' => true,
        'public_information' => true,
        'file_upload' => true,
        'backup_restore' => true,
        'user_roles' => ['kepala_desa', 'sekretaris', 'kaur'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Configuration
    |--------------------------------------------------------------------------
    |
    | File upload settings for documents and images.
    |
    */

    'upload' => [
        'max_file_size' => 2048, // KB
        'allowed_extensions' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'],
        'upload_path' => 'public/uploads/',
        'temp_path' => 'storage/temp/',
        'document_types' => [
            'surat' => 'public/uploads/surat/',
            'pendukung' => 'public/uploads/pendukung/',
            'profil' => 'public/uploads/profil/',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for lists and tables.
    |
    */

    'pagination' => [
        'per_page' => 10,
        'max_per_page' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Date and Time Formats
    |--------------------------------------------------------------------------
    |
    | Date and time display formats used throughout the application.
    |
    */

    'formats' => [
        'date' => 'd/m/Y',
        'datetime' => 'd/m/Y H:i:s',
        'time' => 'H:i:s',
        'month_year' => 'F Y',
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Paths
    |--------------------------------------------------------------------------
    |
    | Various paths used by the application.
    |
    */

    'paths' => [
        'root' => dirname(__DIR__),
        'app' => dirname(__DIR__) . '/app',
        'config' => dirname(__DIR__) . '/config',
        'database' => dirname(__DIR__) . '/database',
        'public' => dirname(__DIR__) . '/public',
        'resources' => dirname(__DIR__) . '/resources',
        'storage' => dirname(__DIR__) . '/storage',
        'views' => dirname(__DIR__) . '/resources/views',
        'uploads' => dirname(__DIR__) . '/public/uploads',
        'temp' => dirname(__DIR__) . '/storage/temp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security-related settings for the application.
    |
    */

    'security' => [
        'session_lifetime' => 120, // minutes
        'password_min_length' => 6,
        'max_login_attempts' => 5,
        'lockout_duration' => 15, // minutes
        'csrf_protection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Letter Services Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for document and letter generation services.
    |
    */

    'letter_services' => [
        'templates_path' => 'resources/templates/',
        'output_path' => 'public/uploads/surat/',
        'numbering_format' => '{number}/{type}/{month}/{year}',
        'available_letters' => [
            'surat_keterangan_domisili' => 'Surat Keterangan Domisili',
            'surat_keterangan_usaha' => 'Surat Keterangan Usaha',
            'surat_keterangan_penghasilan' => 'Surat Keterangan Penghasilan',
            'surat_keterangan_tidak_mampu' => 'Surat Keterangan Tidak Mampu',
            'surat_pengantar_nikah' => 'Surat Pengantar Nikah',
            'surat_keterangan_kelahiran' => 'Surat Keterangan Kelahiran',
            'surat_keterangan_kematian' => 'Surat Keterangan Kematian',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Logging configuration for the application.
    |
    */

    'logging' => [
        'enabled' => true,
        'level' => 'info',
        'path' => 'storage/logs/',
        'max_files' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    |
    | Database and file backup settings.
    |
    */

    'backup' => [
        'enabled' => true,
        'path' => 'storage/backups/',
        'schedule' => 'daily',
        'retention_days' => 30,
        'include_files' => false,
    ],
];
