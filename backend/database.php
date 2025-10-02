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
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }

        $host = getenv('PGHOST');
        $port = getenv('PGPORT') ?: '5432';
        $dbname = getenv('PGDATABASE');
        $user = getenv('PGUSER');
        $password = getenv('PGPASSWORD');

        if (!$host || !$dbname || !$user || !$password) {
            throw new Exception('Database credentials not configured. Please check environment variables (PGHOST, PGDATABASE, PGUSER, PGPASSWORD).');
        }

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 30
        ];

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
