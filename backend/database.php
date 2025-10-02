<?php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Load .env file
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    // Ensure environment variables are loaded, unquoting values
                    $_ENV[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
                }
            }
        }

        // --- Use standard MySQL environment variables ---
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
        $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306'; // Default MySQL port
        $dbname = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE');
        $user = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME'); // Using DB_USERNAME as per your previous .env snippet
        $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
        $ssl_ca = $_ENV['MYSQL_SSL_CA'] ?? getenv('MYSQL_SSL_CA'); // New variable for SSL Certificate path

        if (!$host || !$dbname || !$user || !$password) {
            throw new Exception('Database credentials not configured. Please check environment variables (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD).');
        }

        // --- Change DSN to use 'mysql' driver ---
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 30
        ];
        
        // --- Add SkySQL/MariaDB SSL options if the certificate path is set ---
        if ($ssl_ca) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $ssl_ca;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true; 
        }

        try {
            $this->pdo = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}