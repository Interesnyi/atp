<?php

namespace App\Models;

use App\Core\Model;

class ItemCategory extends Model {
    protected $table = 'items_categories';

    public function getAllCategories() {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        return $this->db->fetchAll($sql);
    }

    public function getCategoryById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function createCategory($data) {
        $sql = "INSERT INTO {$this->table} (name, description, warehouse_type_id, is_deleted) VALUES (?, ?, ?, 0)";
        $this->db->execute($sql, [
            $data['name'],
            $data['description'] ?? null,
            $data['warehouse_type_id'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function updateCategory($id, $data) {
        $sql = "UPDATE {$this->table} SET name = ?, description = ?, warehouse_type_id = ? WHERE id = ?";
        return $this->db->execute($sql, [
            $data['name'],
            $data['description'] ?? null,
            $data['warehouse_type_id'] ?? null,
            $id
        ]) > 0;
    }

    public function deleteCategory($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }

    public function searchCategories($query) {
        $sql = "SELECT c.*, wt.name as warehouse_type_name
                FROM {$this->table} c
                LEFT JOIN warehouse_types wt ON c.warehouse_type_id = wt.id
                WHERE c.is_deleted = 0 AND (c.name LIKE ? OR c.description LIKE ?)
                ORDER BY c.id DESC";
        $like = '%' . $query . '%';
        return $this->db->fetchAll($sql, [$like, $like]);
    }

    public function getCategoriesByWarehouseType($warehouseTypeId) {
        $sql = "SELECT * FROM {$this->table} WHERE warehouse_type_id = ? AND is_deleted = 0 ORDER BY id DESC";
        return $this->db->fetchAll($sql, [$warehouseTypeId]);
    }

    public function getAllCategoriesWithType($filters = []) {
        $sql = "SELECT c.*, wt.name as warehouse_type_name
                FROM {$this->table} c
                LEFT JOIN warehouse_types wt ON c.warehouse_type_id = wt.id
                WHERE c.is_deleted = 0";
        $params = [];
        if (!empty($filters['warehouse_type_id'])) {
            $sql .= " AND c.warehouse_type_id = ?";
            $params[] = $filters['warehouse_type_id'];
        }
        $sql .= " ORDER BY c.id DESC";
        return $this->db->fetchAll($sql, $params);
    }
} 