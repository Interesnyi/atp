<?php
namespace App\Models;

use App\Core\Model;

class Contract extends Model {
    protected $table = 'contracts';

    public function getAll() {
        $sql = "SELECT c.*, cust.company_name, cust.contact_person FROM {$this->table} c JOIN customers cust ON c.customer_id = cust.id ORDER BY c.contract_date DESC, c.id DESC";
        return $this->db->fetchAll($sql);
    }

    public function getById($id) {
        $sql = "SELECT c.*, cust.company_name, cust.contact_person FROM {$this->table} c JOIN customers cust ON c.customer_id = cust.id WHERE c.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (customer_id, contact_person_genitive, contract_number, contract_date, description, contract_file) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['customer_id'],
            $data['contact_person_genitive'] ?? '',
            $data['contract_number'],
            $data['contract_date'],
            $data['description'],
            $data['contract_file'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET customer_id=?, contact_person_genitive=?, contract_number=?, contract_date=?, description=?, contract_file=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['customer_id'],
            $data['contact_person_genitive'] ?? '',
            $data['contract_number'],
            $data['contract_date'],
            $data['description'],
            $data['contract_file'] ?? null,
            $id
        ]) > 0;
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 