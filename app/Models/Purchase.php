<?php
namespace App\Models;
use App\Core\Model;
class Purchase extends Model {
    protected $table = 'purchases';
    public function createPurchase($data) {
        $sql = "INSERT INTO {$this->table} (created_at, status, comment, user_id) VALUES (NOW(), ?, ?, ?)";
        $this->db->execute($sql, [
            $data['status'] ?? 'new',
            $data['comment'] ?? '',
            $data['user_id'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
    public function getPurchaseById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    public function getAllPurchases() {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        return $this->db->fetchAll($sql);
    }
} 