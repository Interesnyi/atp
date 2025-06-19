<?php

namespace App\Models;

use App\Core\Model;

class LegalEntity extends Model {
    protected $table = 'legal_entities';

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0 ORDER BY name";
        return $this->db->fetchAll($sql);
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, inn, kpp, address, phone, email, is_deleted) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $this->db->execute($sql, [
            $data['name'],
            $data['inn'] ?? null,
            $data['kpp'] ?? null,
            $data['address'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET name=?, inn=?, kpp=?, address=?, phone=?, email=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['name'],
            $data['inn'] ?? null,
            $data['kpp'] ?? null,
            $data['address'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $id
        ]) > 0;
    }

    public function delete($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 