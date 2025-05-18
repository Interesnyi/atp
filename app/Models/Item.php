<?php

namespace App\Models;

use App\Core\Model;

class Item extends Model {
    protected $table = 'items';
    
    /**
     * Получение всех товаров
     *
     * @param array $filters Фильтры (category_id, warehouse_id)
     * @return array
     */
    public function getAllItems($filters = []) {
        $sql = "SELECT i.*, c.name as category_name, wt.name as warehouse_type_name
                FROM {$this->table} i
                JOIN categories c ON i.category_id = c.id
                JOIN warehouse_types wt ON c.warehouse_type_id = wt.id
                WHERE i.is_deleted = 0";
        
        $params = [];
        
        // Применение фильтров
        if (!empty($filters['category_id'])) {
            $sql .= " AND i.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['warehouse_type_id'])) {
            $sql .= " AND c.warehouse_type_id = ?";
            $params[] = $filters['warehouse_type_id'];
        }
        
        $sql .= " ORDER BY i.name";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Получение товара по ID
     *
     * @param int $id
     * @return array|false
     */
    public function getItemById($id) {
        $sql = "SELECT i.*, c.name as category_name, wt.name as warehouse_type_name
                FROM {$this->table} i
                JOIN categories c ON i.category_id = c.id
                JOIN warehouse_types wt ON c.warehouse_type_id = wt.id
                WHERE i.id = ? AND i.is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Получение товаров по категории
     *
     * @param int $categoryId
     * @return array
     */
    public function getItemsByCategory($categoryId) {
        $sql = "SELECT * FROM {$this->table} WHERE category_id = ? AND is_deleted = 0";
        return $this->db->fetchAll($sql, [$categoryId]);
    }
    
    /**
     * Создание нового товара
     *
     * @param array $data
     * @return int ID созданного товара
     */
    public function createItem($data) {
        $sql = "INSERT INTO {$this->table} 
                (name, article, category_id, description, unit, has_volume, is_deleted) 
                VALUES (?, ?, ?, ?, ?, ?, 0)";
        $this->db->execute($sql, [
            $data['name'],
            $data['article'] ?? null,
            $data['category_id'],
            $data['description'] ?? null,
            $data['unit'] ?? 'шт',
            !empty($data['has_volume']) ? 1 : 0
        ]);
        
        $itemId = $this->db->lastInsertId();
        
        // Если есть дополнительные свойства, добавляем их
        if (!empty($data['properties']) && is_array($data['properties'])) {
            $this->saveItemProperties($itemId, $data['properties']);
        }
        
        return $itemId;
    }
    
    /**
     * Обновление данных товара
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateItem($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = ?, article = ?, category_id = ?, 
                    description = ?, unit = ?, has_volume = ?
                WHERE id = ?";
        
        $result = $this->db->execute($sql, [
            $data['name'],
            $data['article'] ?? null,
            $data['category_id'],
            $data['description'] ?? null,
            $data['unit'] ?? 'шт',
            !empty($data['has_volume']) ? 1 : 0,
            $id
        ]) > 0;
        
        // Если есть дополнительные свойства, обновляем их
        if (!empty($data['properties']) && is_array($data['properties'])) {
            $this->saveItemProperties($id, $data['properties']);
        }
        
        return $result;
    }
    
    /**
     * Удаление товара (мягкое удаление)
     *
     * @param int $id
     * @return bool
     */
    public function deleteItem($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
    
    /**
     * Сохранение дополнительных свойств товара
     *
     * @param int $itemId
     * @param array $properties
     * @return void
     */
    protected function saveItemProperties($itemId, $properties) {
        // Сначала удаляем существующие свойства
        $sql = "DELETE FROM item_properties WHERE item_id = ?";
        $this->db->execute($sql, [$itemId]);
        
        // Затем добавляем новые
        foreach ($properties as $key => $value) {
            if (empty($value)) {
                continue;
            }
            
            $sql = "INSERT INTO item_properties (item_id, property_key, property_value) 
                    VALUES (?, ?, ?)";
            $this->db->execute($sql, [$itemId, $key, $value]);
        }
    }
    
    /**
     * Получение свойств товара
     *
     * @param int $itemId
     * @return array
     */
    public function getItemProperties($itemId) {
        $sql = "SELECT property_key, property_value 
                FROM item_properties 
                WHERE item_id = ?";
        $properties = $this->db->fetchAll($sql, [$itemId]);
        
        $result = [];
        foreach ($properties as $property) {
            $result[$property['property_key']] = $property['property_value'];
        }
        
        return $result;
    }
} 