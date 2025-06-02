<?php
namespace App\Models;
use App\Core\Model;
class Car extends Model {
    protected $table = 'cars';
    public function getCarsByCustomer($customerId) {
        $sql = "SELECT * FROM {$this->table} WHERE customer_id = ? AND is_deleted = 0 ORDER BY id DESC";
        return $this->db->fetchAll($sql, [$customerId]);
    }
    public function getCarById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    public function getCarByIdWithCustomer($id) {
        $sql = "SELECT car.*, CASE WHEN c.is_individual = 1 THEN c.contact_person ELSE c.company_name END as customer_name FROM {$this->table} car JOIN customers c ON car.customer_id = c.id WHERE car.id = ? AND car.is_deleted = 0 AND c.is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    public function createCar($data) {
        $sql = "INSERT INTO {$this->table} (customer_id, brand, model, year, vin, license_plate, body_number, engine_number, comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['customer_id'], $data['brand'], $data['model'], $data['year'] ?? null, $data['vin'] ?? null,
            $data['license_plate'] ?? null, $data['body_number'] ?? null, $data['engine_number'] ?? null, $data['comment'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
    public function updateCar($id, $data) {
        $sql = "UPDATE {$this->table} SET brand=?, model=?, year=?, vin=?, license_plate=?, body_number=?, engine_number=?, comment=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['brand'], $data['model'], $data['year'] ?? null, $data['vin'] ?? null,
            $data['license_plate'] ?? null, $data['body_number'] ?? null, $data['engine_number'] ?? null, $data['comment'] ?? null, $id
        ]) > 0;
    }
    public function deleteCar($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
    public function getAllCarsWithCustomer() {
        $sql = "SELECT car.*, CASE WHEN c.is_individual = 1 THEN c.contact_person ELSE c.company_name END as customer_name FROM {$this->table} car JOIN customers c ON car.customer_id = c.id WHERE car.is_deleted = 0 AND c.is_deleted = 0 ORDER BY car.id DESC";
        return $this->db->fetchAll($sql);
    }
} 