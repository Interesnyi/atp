<?php
namespace App\Models;
use App\Core\Model;
class OrderWork extends Model {
    protected $table = 'order_works';
    public function getWorksByOrder($orderId) {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ? ORDER BY id";
        return $this->db->fetchAll($sql, [$orderId]);
    }
    public function createWork($data) {
        $sql = "INSERT INTO {$this->table} (order_id, work_type_id, name, code, quantity, price, total, executor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['order_id'], $data['work_type_id'] ?? null, $data['name'], $data['code'] ?? null,
            $data['quantity'] ?? 1, $data['price'] ?? 0, $data['total'] ?? 0, $data['executor'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
    public function deleteWork($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
    public function deleteWorksByOrder($orderId) {
        $sql = "DELETE FROM {$this->table} WHERE order_id = ?";
        return $this->db->execute($sql, [$orderId]);
    }
} 