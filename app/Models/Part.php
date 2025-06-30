<?php
namespace App\Models;

use App\Core\Model;

class Part extends Model {
    protected $table = 'parts';

    public function getAllParts() {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY name");
    }

    public function getPartById($id) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function createPart($data) {
        $sql = "INSERT INTO {$this->table} (article, name, price) VALUES (?, ?, ?)";
        $this->db->execute($sql, [
            $data['article'],
            $data['name'],
            $data['price']
        ]);
        return $this->db->lastInsertId();
    }

    public function updatePart($id, $data) {
        $sql = "UPDATE {$this->table} SET article = ?, name = ?, price = ? WHERE id = ?";
        return $this->db->execute($sql, [
            $data['article'],
            $data['name'],
            $data['price'],
            $id
        ]);
    }

    public function deletePart($id) {
        return $this->db->delete($this->table, $id);
    }
} 