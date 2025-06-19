<?php
namespace App\Models;
use App\Core\Model;
class PurchaseItem extends Model {
    protected $table = 'purchase_items';
    public function createItem($data) {
        $sql = "INSERT INTO {$this->table} (purchase_id, category_id, item_id, quantity) VALUES (?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['purchase_id'],
            $data['category_id'],
            $data['item_id'],
            $data['quantity']
        ]);
        return $this->db->lastInsertId();
    }
    public function getItemsByPurchaseId($purchaseId) {
        $sql = "SELECT pi.*, c.name as category_name, i.name as item_name FROM {$this->table} pi JOIN items_categories c ON pi.category_id = c.id JOIN items i ON pi.item_id = i.id WHERE pi.purchase_id = ?";
        return $this->db->fetchAll($sql, [$purchaseId]);
    }
} 