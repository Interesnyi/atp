<?php

namespace App\Models;

use App\Core\Model;
use App\Models\InvoiceItem;
use App\Models\Operation;

class Invoice extends Model {
    protected $table = 'invoices';

    public function getAll($filters = []) {
        $sql = "SELECT i.*, l.name as legal_entity_name FROM {$this->table} i LEFT JOIN legal_entities l ON i.legal_entity_id = l.id WHERE i.is_deleted = 0";
        $params = [];
        if (!empty($filters['number'])) {
            $sql .= " AND i.number = ?";
            $params[] = $filters['number'];
        }
        if (!empty($filters['legal_entity_id'])) {
            $sql .= " AND i.legal_entity_id = ?";
            $params[] = $filters['legal_entity_id'];
        }
        if (isset($filters['status_issued'])) {
            $sql .= " AND i.status_issued = ?";
            $params[] = $filters['status_issued'];
        }
        if (isset($filters['status_shipped'])) {
            $sql .= " AND i.status_shipped = ?";
            $params[] = $filters['status_shipped'];
        }
        if (isset($filters['status_paid'])) {
            $sql .= " AND i.status_paid = ?";
            $params[] = $filters['status_paid'];
        }
        $sql .= " ORDER BY i.id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function getById($id) {
        $sql = "SELECT i.*, l.name as legal_entity_name FROM {$this->table} i LEFT JOIN legal_entities l ON i.legal_entity_id = l.id WHERE i.id = ? AND i.is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (number, date, legal_entity_id, status_issued, status_shipped, status_paid, date_issued, date_shipped, date_paid, comment, total_amount, is_deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
        $totalAmount = 0;
        $this->db->execute($sql, [
            $data['number'],
            $data['date'],
            $data['legal_entity_id'],
            $data['status_issued'] ?? 0,
            $data['status_shipped'] ?? 0,
            $data['status_paid'] ?? 0,
            $data['date_issued'] ?? null,
            $data['date_shipped'] ?? null,
            $data['date_paid'] ?? null,
            $data['comment'] ?? null,
            $totalAmount
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $operationModel = new Operation();
        $invoice = $this->getById($id);
        $operations = $operationModel->getAllOperations(['document_number' => $invoice['number']]);
        $totalAmount = 0;
        foreach ($operations as $op) {
            $totalAmount += $op['total_cost'];
        }
        $sql = "UPDATE {$this->table} SET number=?, date=?, legal_entity_id=?, status_issued=?, status_shipped=?, status_paid=?, date_issued=?, date_shipped=?, date_paid=?, comment=?, total_amount=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['number'],
            $data['date'],
            $data['legal_entity_id'],
            $data['status_issued'] ?? 0,
            $data['status_shipped'] ?? 0,
            $data['status_paid'] ?? 0,
            $data['date_issued'] ?? null,
            $data['date_shipped'] ?? null,
            $data['date_paid'] ?? null,
            $data['comment'] ?? null,
            $totalAmount,
            $id
        ]) > 0;
    }

    public function delete($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
} 