<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;
    private $config;

    private function __construct() {
        $this->config = require __DIR__ . '/../Config/config.php';
        $this->connect();
    }

    private function connect() {
        try {
            // Выводим информацию о конфигурации при отладке
            if ($this->config['app']['debug']) {
                error_log("Database connection config:");
                error_log("Host: " . $this->config['db']['host']);
                error_log("Database: " . $this->config['db']['database']);
                error_log("User: " . $this->config['db']['user']);
            }

            $dsn = "mysql:host={$this->config['db']['host']};dbname={$this->config['db']['database']};charset={$this->config['db']['charset']}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO(
                $dsn,
                $this->config['db']['user'],
                $this->config['db']['password'],
                $options
            );

            // Проверяем соединение
            $this->connection->query('SELECT 1');
            
            if ($this->config['app']['debug']) {
                error_log("Database connection successful");
            }
        } catch (PDOException $e) {
            $error = "Database connection failed: " . $e->getMessage();
            error_log($error);
            throw new PDOException($error, (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $error = "Query execution failed: " . $e->getMessage() . "\nSQL: " . $sql;
            error_log($error);
            throw new PDOException($error, (int)$e->getCode());
        }
    }

    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
} 