<?php

namespace App\Models;

use App\Core\Model;

class Supplier extends Model {
    protected $table = 'suppliers';
    
    /**
     * Получение всех поставщиков
     *
     * @return array
     */
    public function getAllSuppliers() {
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0 ORDER BY name";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Получение поставщика по ID
     *
     * @param int $id
     * @return array|false
     */
    public function getSupplierById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Создание нового поставщика
     *
     * @param array $data
     * @return int ID созданного поставщика
     */
    public function createSupplier($data) {
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
     * Обновление данных поставщика
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateSupplier($id, $data) {
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
     * Удаление поставщика (мягкое удаление)
     *
     * @param int $id
     * @return bool
     */
    public function deleteSupplier($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
    
    /**
     * Получение списка поставщиков для выпадающего списка
     *
     * @return array
     */
    public function getSuppliersForSelect() {
        $sql = "SELECT id, name FROM {$this->table} WHERE is_deleted = 0 ORDER BY name";
        $suppliers = $this->db->fetchAll($sql);
        
        $result = [];
        foreach ($suppliers as $supplier) {
            $result[$supplier['id']] = $supplier['name'];
        }
        
        return $result;
    }
    
    /**
     * Преобразует существующих поставщиков из старой таблицы в новую
     * 
     * @return bool
     */
    public function migrateFromMaslosklad() {
        try {
            $this->db->beginTransaction();
            
            // Получаем данные из старой таблицы
            $sql = "SELECT * FROM maslosklad_suppliers WHERE is_deleted = 0";
            $oldSuppliers = $this->db->fetchAll($sql);
            
            // Вставляем данные в новую таблицу
            foreach ($oldSuppliers as $supplier) {
                $this->createSupplier([
                    'name' => $supplier['name'],
                    'description' => 'Мигрировано из maslosklad_suppliers'
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log('Ошибка миграции поставщиков: ' . $e->getMessage());
            return false;
        }
    }
} 