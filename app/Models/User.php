<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Helpers\ConfigHelper;

/*
|--------------------------------------------------------------------------
| User Model (Custom)
|--------------------------------------------------------------------------
|
| Model untuk mengelola data user dalam sistem custom
| Sistem Informasi Desa Digital
|
*/

class User
{
    const ROLE_KEPALA_DESA = 'kepala_desa';
    const ROLE_SEKRETARIS = 'sekretaris';
    const ROLE_KAUR = 'kaur';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    private $attributes = [];

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Get database connection
     */
    private static function getConnection()
    {
        try {
            $config = ConfigHelper::database();
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            
            $pdo = new PDO($dsn, $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $pdo;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Find user by email
     */
    public static function findByEmail($email)
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            $demoData = self::getDemoData();
            foreach ($demoData as $user) {
                if ($user->email === $email) {
                    return $user;
                }
            }
            return null;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $result = $stmt->fetch();
            
            return $result ? new self($result) : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Find user by ID
     */
    public static function find($id)
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            $demoData = self::getDemoData();
            foreach ($demoData as $user) {
                if ($user->id == $id) {
                    return $user;
                }
            }
            return null;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            return $result ? new self($result) : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get all users
     */
    public static function all()
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            return self::getDemoData();
        }

        try {
            $stmt = $pdo->query("SELECT * FROM users ORDER BY name ASC");
            $results = $stmt->fetchAll();
            
            return array_map(function($row) {
                return new self($row);
            }, $results);
        } catch (PDOException $e) {
            return self::getDemoData();
        }
    }

    /**
     * Create new user
     */
    public static function create($data)
    {
        $pdo = self::getConnection();
        if (!$pdo) {
            return false;
        }

        try {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $fields = implode(',', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $stmt = $pdo->prepare("INSERT INTO users ({$fields}) VALUES ({$placeholders})");
            return $stmt->execute($data);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update user
     */
    public function update($data)
    {
        $pdo = self::getConnection();
        if (!$pdo || !isset($this->attributes['id'])) {
            return false;
        }

        try {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $setParts = [];
            foreach ($data as $key => $value) {
                $setParts[] = "{$key} = :{$key}";
            }
            $setClause = implode(', ', $setParts);
            
            $data['id'] = $this->attributes['id'];
            $stmt = $pdo->prepare("UPDATE users SET {$setClause} WHERE id = :id");
            
            if ($stmt->execute($data)) {
                foreach ($data as $key => $value) {
                    if ($key !== 'id') {
                        $this->attributes[$key] = $value;
                    }
                }
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verify password
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * Check if user is Kepala Desa
     */
    public function isKepala()
    {
        return $this->role === self::ROLE_KEPALA_DESA;
    }

    /**
     * Check if user is Sekretaris
     */
    public function isSekretaris()
    {
        return $this->role === self::ROLE_SEKRETARIS;
    }

    /**
     * Check if user is Kaur
     */
    public function isKaur()
    {
        return $this->role === self::ROLE_KAUR;
    }

    /**
     * Get role display name
     */
    public function getRoleDisplay()
    {
        return match($this->role) {
            self::ROLE_KEPALA_DESA => 'Kepala Desa',
            self::ROLE_SEKRETARIS => 'Sekretaris',
            self::ROLE_KAUR => 'Kaur',
            default => 'Unknown'
        };
    }

    /**
     * Check if user has permission for a feature
     */
    public function hasPermission($feature)
    {
        $permissions = [
            self::ROLE_KEPALA_DESA => ['view_all', 'edit_all', 'delete_all', 'approve_all'],
            self::ROLE_SEKRETARIS => ['view_all', 'edit_resident', 'process_document'],
            self::ROLE_KAUR => ['view_resident', 'process_document'],
        ];

        return in_array($feature, $permissions[$this->role] ?? []);
    }

    /**
     * Magic getter for attributes
     */
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Magic setter for attributes
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Magic isset for attributes
     */
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Convert to array (excluding sensitive data)
     */
    public function toArray()
    {
        $data = $this->attributes;
        unset($data['password'], $data['remember_token']);
        return $data;
    }

    /**
     * Get demo data when database is not available
     */
    private static function getDemoData()
    {
        return [
            new self([
                'id' => 1,
                'name' => 'Kepala Desa',
                'email' => 'kepala@desa.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => self::ROLE_KEPALA_DESA,
                'phone' => '081234567890',
                'is_active' => 1,
                'created_at' => '2024-01-01 10:00:00',
                'updated_at' => '2024-01-01 10:00:00',
            ]),
            new self([
                'id' => 2,
                'name' => 'Sekretaris Desa',
                'email' => 'sekretaris@desa.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => self::ROLE_SEKRETARIS,
                'phone' => '081234567891',
                'is_active' => 1,
                'created_at' => '2024-01-01 10:00:00',
                'updated_at' => '2024-01-01 10:00:00',
            ]),
            new self([
                'id' => 3,
                'name' => 'Kaur Pemerintahan',
                'email' => 'kaur@desa.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => self::ROLE_KAUR,
                'phone' => '081234567892',
                'is_active' => 1,
                'created_at' => '2024-01-01 10:00:00',
                'updated_at' => '2024-01-01 10:00:00',
            ]),
        ];
    }
}
