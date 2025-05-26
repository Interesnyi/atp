<?php

namespace App\Models;

use App\Core\Model;

class Buyer extends Model {
    protected $table = 'buyers';

    /**
     * Получение всех получателей
     *
     * @return array
     */
    public function getAllBuyers() {
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0 ORDER BY name";
        return $this->db->fetchAll($sql);
    }

    /**
     * Получение получателя по ID
     *
     * @param int $id
     * @return array|false
     */
    public function getBuyerById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Создание нового получателя
     *
     * @param array $data
     * @return int ID созданного получателя
     */
    public function createBuyer($data) {
        $sql = "INSERT INTO {$this->table} 
                (name, contact_person, phone, email, address, description, is_deleted) 
                VALUES (?, ?, ?, ?, ?, ?, 0)";
        $this->db->execute($sql, [
            $data['name'],
            $data['contact_person'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['description'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Обновление данных получателя
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateBuyer($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = ?, contact_person = ?, phone = ?, 
                    email = ?, address = ?, description = ? 
                WHERE id = ?";
        return $this->db->execute($sql, [
            $data['name'],
            $data['contact_person'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['description'] ?? null,
            $id
        ]) > 0;
    }

    /**
     * Удаление получателя (мягкое удаление)
     *
     * @param int $id
     * @return bool
     */
    public function deleteBuyer($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }

    /**
     * Поиск получателей по названию, контактному лицу, телефону, email или адресу
     *
     * @param string $searchTerm Поисковый запрос
     * @return array
     */
    public function searchBuyers($searchTerm) {
        if (empty($searchTerm)) {
            return $this->getAllBuyers();
        }
        $searchParam = '%' . $searchTerm . '%';
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_deleted = 0 AND (
                    name LIKE ? OR 
                    contact_person LIKE ? OR 
                    phone LIKE ? OR 
                    email LIKE ? OR 
                    address LIKE ?
                ) 
                ORDER BY name";
        return $this->db->fetchAll($sql, [
            $searchParam,
            $searchParam,
            $searchParam,
            $searchParam,
            $searchParam
        ]);
    }
} 