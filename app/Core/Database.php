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
        try {
            error_log("DEBUG Database::fetch - SQL: {$sql}");
            error_log("DEBUG Database::fetch - Params: " . json_encode($params));
            
            $stmt = $this->query($sql, $params);
            $result = $stmt->fetch();
            
            error_log("DEBUG Database::fetch - Результат: " . ($result ? json_encode($result) : 'NULL'));
            return $result;
        } catch (PDOException $e) {
            error_log("ОШИБКА Database::fetch - " . $e->getMessage());
            throw $e;
        }
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Возвращает первую строку результата запроса
     */
    public function fetchOne($sql, $params = []) {
        try {
            error_log("DEBUG Database::fetchOne - SQL: {$sql}");
            error_log("DEBUG Database::fetchOne - Params: " . json_encode($params));
            
            $stmt = $this->query($sql, $params);
            $result = $stmt->fetch();
            
            error_log("DEBUG Database::fetchOne - Результат: " . ($result ? json_encode($result) : 'NULL'));
            return $result;
        } catch (PDOException $e) {
            error_log("ОШИБКА Database::fetchOne - " . $e->getMessage());
            throw $e;
        }
    }
} 