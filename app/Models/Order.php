<?php

namespace App\Models;

use App\Core\Model;

class Order extends Model {
    protected $table = 'orders';

    public function getAllOrders() {
        $sql = "SELECT o.*, 
            CASE WHEN c.is_individual = 1 THEN c.contact_person ELSE c.company_name END as customer_name, 
            car.brand, car.model, car.license_plate, co.contract_number, co.contract_date
            FROM {$this->table} o
            JOIN customers c ON o.customer_id = c.id
            JOIN cars car ON o.car_id = car.id
            LEFT JOIN contracts co ON o.contract_id = co.id
            ORDER BY o.date_created DESC";
        return $this->db->fetchAll($sql);
    }

    public function getOrderById($id) {
        $sql = "SELECT o.*, 
            CASE WHEN c.is_individual = 1 THEN c.contact_person ELSE c.company_name END as customer_name, 
            car.brand, car.model, car.license_plate, co.contract_number, co.contract_date
            FROM {$this->table} o
            JOIN customers c ON o.customer_id = c.id
            JOIN cars car ON o.car_id = car.id
            LEFT JOIN contracts co ON o.contract_id = co.id
            WHERE o.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function createOrder($data) {
        $sql = "INSERT INTO {$this->table} (customer_id, car_id, contract_id, order_number, date_created, date_completed, manager, status, comment)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['customer_id'],
            $data['car_id'],
            !empty($data['contract_id']) ? $data['contract_id'] : null,
            $data['order_number'],
            $data['date_created'] ?? date('Y-m-d H:i:s'),
            $data['date_completed'] ?? null,
            $data['manager'] ?? null,
            $data['status'] ?? 'new',
            $data['comment'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function updateOrder($id, $data) {
        $sql = "UPDATE {$this->table} SET customer_id=?, car_id=?, contract_id=?, order_number=?, date_created=?, date_completed=?, manager=?, status=?, comment=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['customer_id'],
            $data['car_id'],
            !empty($data['contract_id']) ? $data['contract_id'] : null,
            $data['order_number'],
            $data['date_created'] ?? date('Y-m-d H:i:s'),
            $data['date_completed'] ?? null,
            $data['manager'] ?? null,
            $data['status'] ?? 'new',
            $data['comment'] ?? null,
            $id
        ]) > 0;
    }

    public function deleteOrder($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }

    // Связанные данные
    public function getWorks($orderId) {
        $workModel = new OrderWork();
        return $workModel->getWorksByOrder($orderId);
    }
    public function getMaterials($orderId) {
        $matModel = new OrderMaterial();
        return $matModel->getMaterialsByOrder($orderId);
    }
    public function getCustomerMaterials($orderId) {
        $cmModel = new OrderCustomerMaterial();
        return $cmModel->getCustomerMaterialsByOrder($orderId);
    }
    public function getFiles($orderId) {
        $fileModel = new OrderFile();
        return $fileModel->getFilesByOrder($orderId);
    }
} 