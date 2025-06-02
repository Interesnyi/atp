<?php
namespace App\Models;
use App\Core\Model;
class CustomerMaterial extends Model {
    protected $table = 'customer_materials';
    public function getAllCustomerMaterials() {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        return $this->db->fetchAll($sql);
    }
    public function getCustomerMaterialById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }
    public function createCustomerMaterial($data) {
        $sql = "INSERT INTO {$this->table} (name) VALUES (?)";
        $this->db->execute($sql, [$data['name']]);
        return $this->db->lastInsertId();
    }
    public function updateCustomerMaterial($id, $data) {
        $sql = "UPDATE {$this->table} SET name=? WHERE id=?";
        return $this->db->execute($sql, [$data['name'], $id]) > 0;
    }
    public function deleteCustomerMaterial($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 