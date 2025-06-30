<?php
namespace App\Models;

use App\Core\Model;

class InspectionAct extends Model {
    protected $table = 'inspection_acts';

    public function getAll() {
        $sql = "SELECT ia.*, c.company_name, c.contact_person, car.brand, car.model, car.year, car.license_plate, co.contract_number, co.contract_date
                FROM {$this->table} ia
                JOIN customers c ON ia.customer_id = c.id
                JOIN cars car ON ia.car_id = car.id
                LEFT JOIN contracts co ON ia.contract_id = co.id
                ORDER BY ia.date DESC, ia.id DESC";
        return $this->db->fetchAll($sql);
    }

    public function getById($id) {
        $sql = "SELECT ia.*, c.company_name, c.contact_person, car.brand, car.model, car.year, car.license_plate, co.contract_number, co.contract_date
                FROM {$this->table} ia
                JOIN customers c ON ia.customer_id = c.id
                JOIN cars car ON ia.car_id = car.id
                LEFT JOIN contracts co ON ia.contract_id = co.id
                WHERE ia.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (date, customer_id, car_id, contract_id, description, damages, conclusion) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['date'],
            $data['customer_id'],
            $data['car_id'],
            !empty($data['contract_id']) ? $data['contract_id'] : null,
            $data['description'],
            $data['damages'],
            $data['conclusion'],
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET date=?, customer_id=?, car_id=?, contract_id=?, description=?, damages=?, conclusion=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['date'],
            $data['customer_id'],
            $data['car_id'],
            !empty($data['contract_id']) ? $data['contract_id'] : null,
            $data['description'],
            $data['damages'],
            $data['conclusion'],
            $id
        ]) > 0;
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 