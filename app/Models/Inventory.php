<?php

namespace App\Models;

use App\Core\Model;

class Inventory extends Model {
    protected $table = 'inventory';
    
    /**
     * Получение всех инвентарных остатков
     *
     * @param array $filters Фильтры (warehouse_id, category_id, warehouse_type_id)
     * @return array
     */
    public function getAllInventory($filters = []) {
        $sql = "SELECT i.*, 
                    it.name as item_name, 
                    it.article as item_article,
                    it.has_volume,
                    it.unit,
                    c.name as category_name,
                    c.warehouse_type_id,
                    w.name as warehouse_name,
                    wt.name as warehouse_type_name
                FROM {$this->table} i
                JOIN items it ON i.item_id = it.id
                JOIN items_categories c ON it.category_id = c.id
                JOIN warehouses w ON i.warehouse_id = w.id
                JOIN warehouse_types wt ON c.warehouse_type_id = wt.id
                WHERE it.is_deleted = 0 AND w.is_deleted = 0";
        
        $params = [];
        
        // Применение фильтров
        if (!empty($filters['warehouse_id'])) {
            $sql .= " AND i.warehouse_id = ?";
            $params[] = $filters['warehouse_id'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND it.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['warehouse_type_id'])) {
            $sql .= " AND c.warehouse_type_id = ?";
            $params[] = $filters['warehouse_type_id'];
        }
        
        if (isset($filters['has_volume']) && $filters['has_volume'] !== null) {
            $sql .= " AND it.has_volume = ?";
            $params[] = $filters['has_volume'];
        }
        
        if (isset($filters['non_zero_only']) && $filters['non_zero_only']) {
            $sql .= " AND (i.quantity > 0 OR i.volume > 0)";
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (it.name LIKE ? OR it.article LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY wt.name, w.name, c.name, it.name";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Получение инвентарного остатка для конкретного товара на складе
     *
     * @param int $itemId
     * @param int $warehouseId
     * @return array|false
     */
    public function getInventoryItem($itemId, $warehouseId) {
        $sql = "SELECT i.*, 
                    it.name as item_name, 
                    it.article as item_article,
                    c.name as category_name,
                    w.name as warehouse_name
                FROM {$this->table} i
                JOIN items it ON i.item_id = it.id
                JOIN warehouses w ON i.warehouse_id = w.id
                JOIN items_categories c ON it.category_id = c.id
                WHERE i.item_id = ? AND i.warehouse_id = ? 
                    AND it.is_deleted = 0 AND w.is_deleted = 0";
        return $this->db->fetch($sql, [$itemId, $warehouseId]);
    }
    
    /**
     * Получение остатков товара по всем складам
     *
     * @param int $itemId
     * @return array
     */
    public function getItemInventory($itemId) {
        $sql = "SELECT i.*, 
                    w.name as warehouse_name,
                    wt.name as warehouse_type_name
                FROM {$this->table} i
                JOIN warehouses w ON i.warehouse_id = w.id
                JOIN warehouse_types wt ON w.type_id = wt.id
                WHERE i.item_id = ? AND w.is_deleted = 0
                ORDER BY wt.name, w.name";
        return $this->db->fetchAll($sql, [$itemId]);
    }
    
    /**
     * Получение остатков для всех товаров на указанном складе
     *
     * @param int $warehouseId
     * @return array
     */
    public function getWarehouseInventory($warehouseId) {
        $sql = "SELECT i.*, 
                    it.name as item_name, 
                    it.article as item_article,
                    c.name as category_name
                FROM {$this->table} i
                JOIN items it ON i.item_id = it.id
                JOIN items_categories c ON it.category_id = c.id
                WHERE i.warehouse_id = ? AND it.is_deleted = 0
                ORDER BY c.name, it.name";
        return $this->db->fetchAll($sql, [$warehouseId]);
    }
    
    /**
     * Обновление инвентарного остатка
     *
     * @param int $itemId
     * @param int $warehouseId
     * @param float $quantity
     * @param float|null $volume
     * @return bool
     */
    public function updateInventory($itemId, $warehouseId, $quantity, $volume = null) {
        // Проверяем, существует ли такая запись
        $inventory = $this->getInventoryItem($itemId, $warehouseId);
        
        if ($inventory) {
            $sql = "UPDATE {$this->table} 
                    SET quantity = ?, volume = ?, last_update = NOW() 
                    WHERE item_id = ? AND warehouse_id = ?";
            return $this->db->execute($sql, [$quantity, $volume, $itemId, $warehouseId]) > 0;
        } else {
            $sql = "INSERT INTO {$this->table} (item_id, warehouse_id, quantity, volume, last_update) 
                    VALUES (?, ?, ?, ?, NOW())";
            $this->db->execute($sql, [$itemId, $warehouseId, $quantity, $volume]);
            return $this->db->lastInsertId() > 0;
        }
    }
    
    /**
     * Установка нулевых остатков для всех товаров на складе
     * (например, при инвентаризации)
     *
     * @param int $warehouseId
     * @return bool
     */
    public function resetWarehouseInventory($warehouseId) {
        $sql = "UPDATE {$this->table} SET quantity = 0, volume = 0, last_update = NOW() 
                WHERE warehouse_id = ?";
        return $this->db->execute($sql, [$warehouseId]) !== false;
    }
    
    /**
     * Миграция данных из старой системы (из таблиц maslosklad_)
     *
     * @return bool
     */
    public function migrateFromMaslosklad() {
        try {
            $this->db->beginTransaction();
            
            // Здесь будет код для миграции данных из старых таблиц в новые
            // Нужно будет сопоставить товары и склады между старой и новой системой
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log('Ошибка миграции инвентаря: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Получение отчета по текущим остаткам
     *
     * @param int|null $warehouseId ID склада (если null, то по всем складам)
     * @return array
     */
    public function getInventoryReport($warehouseId = null) {
        $sql = "SELECT i.*, 
                       items.name as item_name, 
                       items.article, 
                       items.unit, 
                       items_categories.name as category_name,
                       warehouses.name as warehouse_name,
                       warehouse_types.name as warehouse_type_name
                FROM {$this->table} i
                JOIN items ON i.item_id = items.id
                JOIN warehouses ON i.warehouse_id = warehouses.id
                JOIN warehouse_types ON warehouses.type_id = warehouse_types.id
                LEFT JOIN items_categories ON items.category_id = items_categories.id
                WHERE i.quantity > 0";
        
        if ($warehouseId) {
            $sql .= " AND i.warehouse_id = ?";
            $params = [$warehouseId];
        } else {
            $params = [];
        }
        
        $sql .= " ORDER BY warehouses.name, items_categories.name, items.name";
        
        return $this->db->fetchAll($sql, $params);
    }
} 