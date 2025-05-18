<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model {
    protected $table = 'categories';
    
    /**
     * Получение всех категорий
     *
     * @param int|null $warehouseTypeId Фильтр по типу склада
     * @return array
     */
    public function getAllCategories($warehouseTypeId = null) {
        $sql = "SELECT c.*, wt.name as warehouse_type_name 
                FROM {$this->table} c
                JOIN warehouse_types wt ON c.warehouse_type_id = wt.id
                WHERE c.is_deleted = 0";
        
        $params = [];
        
        if ($warehouseTypeId !== null) {
            $sql .= " AND c.warehouse_type_id = ?";
            $params[] = $warehouseTypeId;
        }
        
        $sql .= " ORDER BY wt.name, c.name";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Получение категории по ID
     *
     * @param int $id
     * @return array|false
     */
    public function getCategoryById($id) {
        $sql = "SELECT c.*, wt.name as warehouse_type_name 
                FROM {$this->table} c
                JOIN warehouse_types wt ON c.warehouse_type_id = wt.id
                WHERE c.id = ? AND c.is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Получение категорий по типу склада
     *
     * @param int $warehouseTypeId
     * @return array
     */
    public function getCategoriesByWarehouseType($warehouseTypeId) {
        $sql = "SELECT * FROM {$this->table} WHERE warehouse_type_id = ? AND is_deleted = 0 ORDER BY name";
        return $this->db->fetchAll($sql, [$warehouseTypeId]);
    }
    
    /**
     * Создание новой категории
     *
     * @param array $data
     * @return int ID созданной категории
     */
    public function createCategory($data) {
        $sql = "INSERT INTO {$this->table} (name, warehouse_type_id, description, is_deleted) 
                VALUES (?, ?, ?, 0)";
        $this->db->execute($sql, [
            $data['name'],
            $data['warehouse_type_id'],
            $data['description'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Обновление данных категории
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateCategory($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = ?, warehouse_type_id = ?, description = ? 
                WHERE id = ?";
        return $this->db->execute($sql, [
            $data['name'],
            $data['warehouse_type_id'],
            $data['description'] ?? null,
            $id
        ]) > 0;
    }
    
    /**
     * Удаление категории (мягкое удаление)
     *
     * @param int $id
     * @return bool
     */
    public function deleteCategory($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 