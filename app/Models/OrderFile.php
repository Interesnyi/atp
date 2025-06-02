<?php
namespace App\Models;
use App\Core\Model;
class OrderFile extends Model {
    protected $table = 'order_files';
    public function getFilesByOrder($orderId) {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ? ORDER BY uploaded_at";
        return $this->db->fetchAll($sql, [$orderId]);
    }
    public function createFile($data) {
        $sql = "INSERT INTO {$this->table} (order_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['order_id'], $data['file_name'], $data['file_path'], $data['file_type'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
    public function deleteFile($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 