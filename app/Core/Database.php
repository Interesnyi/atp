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
            // Закомментированы все file_put_contents для debug.log
            // if ($this->config['app']['debug']) {
            //     file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Database connection config:" . PHP_EOL, FILE_APPEND);
            //     file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Host: " . $this->config['db']['host'] . PHP_EOL, FILE_APPEND);
            //     file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Database: " . $this->config['db']['database'] . PHP_EOL, FILE_APPEND);
            //     file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "User: " . $this->config['db']['user'] . PHP_EOL, FILE_APPEND);
            //     file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Charset: " . $this->config['db']['charset'] . PHP_EOL, FILE_APPEND);
            //     file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Collation: " . $this->config['db']['collation'] . PHP_EOL, FILE_APPEND);
            // }

            $dsn = "mysql:host={$this->config['db']['host']};dbname={$this->config['db']['database']}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->config['db']['charset']} COLLATE {$this->config['db']['collation']}; 
                                                SET character_set_client = {$this->config['db']['charset']};
                                                SET character_set_results = {$this->config['db']['charset']};
                                                SET character_set_connection = {$this->config['db']['charset']};
                                                SET collation_connection = {$this->config['db']['collation']};"
            ];

            $this->connection = new PDO(
                $dsn,
                $this->config['db']['user'],
                $this->config['db']['password'],
                $options
            );

            // Дополнительно устанавливаем кодировку для текущей сессии
            $this->connection->exec("SET CHARACTER SET {$this->config['db']['charset']}");
            $this->connection->exec("SET NAMES {$this->config['db']['charset']} COLLATE {$this->config['db']['collation']}");
            $this->connection->exec("SET character_set_client = {$this->config['db']['charset']}");
            $this->connection->exec("SET character_set_results = {$this->config['db']['charset']}");
            $this->connection->exec("SET character_set_connection = {$this->config['db']['charset']}");
            $this->connection->exec("SET collation_connection = {$this->config['db']['collation']}");
            
            // Проверяем соединение
            $this->connection->query('SELECT 1');
            
            // Закомментированы все file_put_contents для debug.log
            // if ($this->config['app']['debug']) {
            //     file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Database connection successful" . PHP_EOL, FILE_APPEND);
            // }
        } catch (PDOException $e) {
            $error = "Database connection failed: " . $e->getMessage();
            // Закомментированы все file_put_contents для debug.log
            // file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . $error . PHP_EOL, FILE_APPEND);
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
            if ($this->config['app']['debug']) {
                error_log("DEBUG Database::fetch - SQL: {$sql}");
                error_log("DEBUG Database::fetch - Params: " . json_encode($params));
            }
            
            $stmt = $this->query($sql, $params);
            $result = $stmt->fetch();
            
            if ($this->config['app']['debug']) {
                error_log("DEBUG Database::fetch - Результат: " . ($result ? json_encode($result) : 'NULL'));
            }
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
            if ($this->config['app']['debug']) {
                error_log("DEBUG Database::fetchOne - SQL: {$sql}");
                error_log("DEBUG Database::fetchOne - Params: " . json_encode($params));
            }
            
            $stmt = $this->query($sql, $params);
            $result = $stmt->fetch();
            
            if ($this->config['app']['debug']) {
                error_log("DEBUG Database::fetchOne - Результат: " . ($result ? json_encode($result) : 'NULL'));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("ОШИБКА Database::fetchOne - " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Выполняет запрос к базе данных без возврата результата (INSERT, UPDATE, DELETE)
     */
    public function execute($sql, $params = []) {
        try {
            if ($this->config['app']['debug']) {
                error_log("DEBUG Database::execute - SQL: {$sql}");
                error_log("DEBUG Database::execute - Params: " . json_encode($params));
            }
            
            $stmt = $this->query($sql, $params);
            $rowCount = $stmt->rowCount();
            
            if ($this->config['app']['debug']) {
                error_log("DEBUG Database::execute - Affected rows: {$rowCount}");
            }
            return $rowCount;
        } catch (PDOException $e) {
            error_log("ОШИБКА Database::execute - " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Начало транзакции
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Подтверждение транзакции
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Откат транзакции
     */
    public function rollback() {
        return $this->connection->rollBack();
    }
} 