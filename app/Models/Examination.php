<?php
namespace App\Models;

use App\Core\Model;

class Examination extends Model {
    protected $table = 'examinations';

    public function getAll() {
        $sql = "SELECT e.*, c.company_name, c.contact_person, car.brand, car.model, car.year, car.license_plate, co.contract_number, co.contract_date
                FROM {$this->table} e
                JOIN customers c ON e.customer_id = c.id
                JOIN cars car ON e.car_id = car.id
                LEFT JOIN contracts co ON e.contract_id = co.id
                ORDER BY e.date DESC, e.id DESC";
        return $this->db->fetchAll($sql);
    }

    public function getById($id) {
        $sql = "SELECT e.*, c.company_name, c.contact_person, car.brand, car.model, car.year, car.license_plate, co.contract_number, co.contract_date
                FROM {$this->table} e
                JOIN customers c ON e.customer_id = c.id
                JOIN cars car ON e.car_id = car.id
                LEFT JOIN contracts co ON e.contract_id = co.id
                WHERE e.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (date, customer_id, car_id, contract_id) VALUES (?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['date'],
            $data['customer_id'],
            $data['car_id'],
            !empty($data['contract_id']) ? $data['contract_id'] : null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET date=?, customer_id=?, car_id=?, contract_id=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['date'],
            $data['customer_id'],
            $data['car_id'],
            !empty($data['contract_id']) ? $data['contract_id'] : null,
            $id
        ]) > 0;
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 