<?php

namespace App\Core;

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getTableName() {
        return $this->table;
    }

    public function find($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
            error_log("DEBUG Model::find - SQL запрос: {$sql}, параметры: " . print_r([$id], true));
            
            $result = $this->db->fetch($sql, [$id]);
            
            error_log("DEBUG Model::find - Результат: " . ($result ? json_encode($result) : 'NULL'));
            return $result;
        } catch (\Exception $e) {
            error_log("ОШИБКА в Model::find - " . $e->getMessage());
            error_log("Трассировка: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

    public function create($data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $fields),
            $placeholders
        );

        $this->db->query($sql, $values);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        $set = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $this->table,
            $set,
            $this->primaryKey
        );

        $values[] = $id;
        return $this->db->query($sql, $values);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->query($sql, [$id]);
    }

    public function where($conditions, $params = []) {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions}";
        return $this->db->fetchAll($sql, $params);
    }

    public function findOne($conditions, $params = []) {
        // Проверяем, является ли $conditions полным SQL запросом
        if (stripos($conditions, 'SELECT') === 0) {
            // Если это полный запрос, используем его как есть
            $sql = $conditions;
        } else {
            // Если это только условие WHERE, формируем полный запрос
            $sql = "SELECT * FROM {$this->table} WHERE {$conditions} LIMIT 1";
        }
        return $this->db->fetch($sql, $params);
    }
} 