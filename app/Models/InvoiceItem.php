<?php

namespace App\Models;

use App\Core\Model;

class InvoiceItem extends Model {
    protected $table = 'invoice_items';

    public function getByInvoice($invoice_id) {
        $sql = "SELECT ii.*, o.*, i.name as item_name FROM {$this->table} ii JOIN operations o ON ii.operation_id = o.id JOIN items i ON o.item_id = i.id WHERE ii.invoice_id = ?";
        return $this->db->fetchAll($sql, [$invoice_id]);
    }

    public function add($invoice_id, $operation_id, $price) {
        $sql = "INSERT INTO {$this->table} (invoice_id, operation_id, price) VALUES (?, ?, ?)";
        $this->db->execute($sql, [$invoice_id, $operation_id, $price]);
        return $this->db->lastInsertId();
    }

    public function deleteByInvoice($invoice_id) {
        $sql = "DELETE FROM {$this->table} WHERE invoice_id = ?";
        return $this->db->execute($sql, [$invoice_id]);
    }
} 