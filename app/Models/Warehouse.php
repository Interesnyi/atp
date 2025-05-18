<?php

namespace App\Models;

use App\Core\Model;

class Warehouse extends Model {
    protected $table = 'warehouses';
    
    /**
     * Получение всех активных складов
     *
     * @return array
     */
    public function getAllWarehouses() {
        $sql = "SELECT w.*, wt.name as warehouse_type_name 
                FROM {$this->table} w
                JOIN warehouse_types wt ON w.type_id = wt.id
                WHERE w.is_deleted = 0
                ORDER BY w.name";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Получение склада по ID
     *
     * @param int $id
     * @return array|false
     */
    public function getWarehouseById($id) {
        $sql = "SELECT w.*, wt.name as warehouse_type_name 
                FROM {$this->table} w
                JOIN warehouse_types wt ON w.type_id = wt.id
                WHERE w.id = ? AND w.is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Получение складов по типу
     *
     * @param int $typeId
     * @return array
     */
    public function getWarehousesByType($typeId) {
        $sql = "SELECT * FROM {$this->table} WHERE type_id = ? AND is_deleted = 0";
        return $this->db->fetchAll($sql, [$typeId]);
    }
    
    /**
     * Создание нового склада
     *
     * @param array $data
     * @return int ID созданного склада
     */
    public function createWarehouse($data) {
        $sql = "INSERT INTO {$this->table} (name, description, type_id, location, is_deleted) 
                VALUES (?, ?, ?, ?, 0)";
        $this->db->execute($sql, [
            $data['name'],
            $data['description'],
            $data['type_id'],
            $data['location']
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Обновление данных склада
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateWarehouse($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = ?, description = ?, type_id = ?, location = ? 
                WHERE id = ?";
        return $this->db->execute($sql, [
            $data['name'],
            $data['description'],
            $data['type_id'],
            $data['location'],
            $id
        ]) > 0;
    }
    
    /**
     * Удаление склада (мягкое удаление)
     *
     * @param int $id
     * @return bool
     */
    public function deleteWarehouse($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 