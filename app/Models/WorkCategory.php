<?php
namespace App\Models;
use App\Core\Model;
class WorkCategory extends Model {
    protected $table = 'work_categories';
    public function getAllCategories() {
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0 ORDER BY name";
        return $this->db->fetchAll($sql);
    }
    public function getCategoryById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    public function createCategory($data) {
        $sql = "INSERT INTO {$this->table} (name, description) VALUES (?, ?)";
        $this->db->execute($sql, [
            $data['name'],
            $data['description'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
    public function updateCategory($id, $data) {
        $sql = "UPDATE {$this->table} SET name=?, description=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['name'],
            $data['description'] ?? null,
            $id
        ]) > 0;
    }
    public function deleteCategory($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 