<?php
namespace App\Models;
use App\Core\Model;
class WorkType extends Model {
    protected $table = 'work_types';
    public function getAllWorkTypes() {
        $sql = "SELECT wt.*, c.name as category_name FROM {$this->table} wt LEFT JOIN work_categories c ON wt.category_id = c.id WHERE wt.is_deleted = 0 ORDER BY wt.name";
        return $this->db->fetchAll($sql);
    }
    public function getWorkTypeById($id) {
        $sql = "SELECT wt.*, c.name as category_name FROM {$this->table} wt LEFT JOIN work_categories c ON wt.category_id = c.id WHERE wt.id = ? AND wt.is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    public function createWorkType($data) {
        $sql = "INSERT INTO {$this->table} (category_id, name, code, price) VALUES (?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['category_id'] ?? null,
            $data['name'],
            $data['code'] ?? null,
            $data['price'] ?? 0
        ]);
        return $this->db->lastInsertId();
    }
    public function updateWorkType($id, $data) {
        $sql = "UPDATE {$this->table} SET category_id=?, name=?, code=?, price=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['category_id'] ?? null,
            $data['name'],
            $data['code'] ?? null,
            $data['price'] ?? 0,
            $id
        ]) > 0;
    }
    public function deleteWorkType($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 