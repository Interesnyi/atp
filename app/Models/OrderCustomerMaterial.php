<?php
namespace App\Models;
use App\Core\Model;
class OrderCustomerMaterial extends Model {
    protected $table = 'order_customer_materials';
    public function getCustomerMaterialsByOrder($orderId) {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ? ORDER BY id";
        return $this->db->fetchAll($sql, [$orderId]);
    }
    public function createCustomerMaterial($data) {
        $sql = "INSERT INTO {$this->table} (order_id, customer_material_id, name, quantity) VALUES (?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['order_id'], $data['customer_material_id'] ?? null, $data['name'], $data['quantity'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }
    public function deleteCustomerMaterial($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 