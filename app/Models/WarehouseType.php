<?php

namespace App\Models;

use App\Core\Model;

class WarehouseType extends Model {
    protected $table = 'warehouse_types';
    
    /**
     * Получение всех типов складов
     *
     * @return array
     */
    public function getAllTypes() {
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0 ORDER BY name";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Получение типа склада по ID
     *
     * @param int $id
     * @return array|false
     */
    public function getTypeById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Получение типа склада по коду
     *
     * @param string $code
     * @return array|false
     */
    public function getTypeByCode($code) {
        $sql = "SELECT * FROM {$this->table} WHERE code = ? AND is_deleted = 0";
        return $this->db->fetch($sql, [$code]);
    }
    
    /**
     * Создание нового типа склада
     *
     * @param array $data
     * @return int ID созданного типа
     */
    public function createType($data) {
        $sql = "INSERT INTO {$this->table} (name, code, description, is_deleted) 
                VALUES (?, ?, ?, 0)";
        $this->db->execute($sql, [
            $data['name'],
            $data['code'],
            $data['description'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Обновление данных типа склада
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateType($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = ?, description = ? 
                WHERE id = ?";
        return $this->db->execute($sql, [
            $data['name'],
            $data['description'] ?? null,
            $id
        ]) > 0;
    }
    
    /**
     * Удаление типа склада (мягкое удаление)
     *
     * @param int $id
     * @return bool
     */
    public function deleteType($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 