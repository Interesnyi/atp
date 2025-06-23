<?php

namespace App\Models;

use App\Core\Model;

class InvoiceFile extends Model {
    protected $table = 'invoice_files';

    public function getByInvoice($invoice_id) {
        $sql = "SELECT * FROM {$this->table} WHERE invoice_id = ? ORDER BY uploaded_at DESC";
        return $this->db->fetchAll($sql, [$invoice_id]);
    }

    public function add($invoice_id, $file_path, $file_type) {
        $file_name = basename($file_path);
        $sql = "INSERT INTO {$this->table} (invoice_id, file_path, file_type, file_name, uploaded_at) VALUES (?, ?, ?, ?, NOW())";
        $this->db->execute($sql, [$invoice_id, $file_path, $file_type, $file_name]);
        return $this->db->lastInsertId();
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    public function getByType($invoice_id, $file_type) {
        $sql = "SELECT * FROM {$this->table} WHERE invoice_id = ? AND file_type = ? ORDER BY uploaded_at DESC";
        return $this->db->fetchAll($sql, [$invoice_id, $file_type]);
    }
} 