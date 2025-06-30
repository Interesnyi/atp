<?php
namespace App\Models;

use App\Core\Model;

class OrderPart extends Model {
    protected $table = 'order_parts';

    public function getParts($orderId) {
        $sql = "SELECT op.*, p.article, p.name FROM {$this->table} op JOIN parts p ON op.part_id = p.id WHERE op.order_id = ?";
        return $this->db->fetchAll($sql, [$orderId]);
    }

    public function createPart($data) {
        $sql = "INSERT INTO {$this->table} (order_id, part_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['order_id'],
            $data['part_id'],
            $data['quantity'],
            $data['price'],
            $data['total']
        ]);
    }

    public function deletePartsByOrder($orderId) {
        $sql = "DELETE FROM {$this->table} WHERE order_id = ?";
        $this->db->execute($sql, [$orderId]);
    }
} 