<?php
namespace App\Models;
use App\Core\Model;
class OrderMaterial extends Model {
    protected $table = 'order_materials';
    public function getMaterialsByOrder($orderId) {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ? ORDER BY id";
        return $this->db->fetchAll($sql, [$orderId]);
    }
    public function createMaterial($data) {
        $sql = "INSERT INTO {$this->table} (order_id, item_id, name, code, quantity, price, total) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['order_id'], $data['item_id'] ?? null, $data['name'], $data['code'] ?? null,
            $data['quantity'] ?? 1, $data['price'] ?? 0, $data['total'] ?? 0
        ]);
        return $this->db->lastInsertId();
    }
    public function deleteMaterial($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 