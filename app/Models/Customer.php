<?php

namespace App\Models;

use App\Core\Model;

class Customer extends Model {
    protected $table = 'customers';
    
    /**
     * Получение всех покупателей
     *
     * @return array
     */
    public function getAllCustomers() {
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0 ORDER BY company_name, contact_person";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Получение покупателя по ID
     *
     * @param int $id
     * @return array|false
     */
    public function getCustomerById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Создание нового покупателя
     *
     * @param array $data
     * @return int ID созданного покупателя
     */
    public function createCustomer($data) {
        $sql = "INSERT INTO {$this->table} (company_name, contact_person, position, is_individual, phone, email, address, inn, ogrn, bank_name, bik, account_number, correspondent_account, org_card_file, description, is_deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
        $this->db->execute($sql, [
            $data['company_name'] ?? '',
            $data['contact_person'] ?? '',
            $data['position'] ?? '',
            !empty($data['is_individual']) ? 1 : 0,
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['address'] ?? '',
            $data['inn'] ?? '',
            $data['ogrn'] ?? '',
            $data['bank_name'] ?? '',
            $data['bik'] ?? '',
            $data['account_number'] ?? '',
            $data['correspondent_account'] ?? '',
            $data['org_card_file'] ?? '',
            $data['description'] ?? ''
        ]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Обновление данных покупателя
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateCustomer($id, $data) {
        $sql = "UPDATE {$this->table} SET company_name=?, contact_person=?, position=?, is_individual=?, phone=?, email=?, address=?, inn=?, ogrn=?, bank_name=?, bik=?, account_number=?, correspondent_account=?, org_card_file=?, description=? WHERE id=?";
        return $this->db->execute($sql, [
            $data['company_name'] ?? '',
            $data['contact_person'] ?? '',
            $data['position'] ?? '',
            !empty($data['is_individual']) ? 1 : 0,
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['address'] ?? '',
            $data['inn'] ?? '',
            $data['ogrn'] ?? '',
            $data['bank_name'] ?? '',
            $data['bik'] ?? '',
            $data['account_number'] ?? '',
            $data['correspondent_account'] ?? '',
            $data['org_card_file'] ?? '',
            $data['description'] ?? '',
            $id
        ]) > 0;
    }
    
    /**
     * Удаление покупателя (мягкое удаление)
     *
     * @param int $id
     * @return bool
     */
    public function deleteCustomer($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
    
    /**
     * Получение списка покупателей для выпадающего списка
     *
     * @param bool $onlyInternal Только внутренние получатели
     * @return array
     */
    public function getCustomersForSelect($onlyInternal = false) {
        $sql = "SELECT id, name FROM {$this->table} WHERE is_deleted = 0";
        
        if ($onlyInternal) {
            $sql .= " AND is_internal = 1";
        }
        
        $sql .= " ORDER BY name";
        $customers = $this->db->fetchAll($sql);
        
        $result = [];
        foreach ($customers as $customer) {
            $result[$customer['id']] = $customer['name'];
        }
        
        return $result;
    }
    
    /**
     * Преобразует существующих покупателей из старой таблицы в новую
     * 
     * @return bool
     */
    public function migrateFromMaslosklad() {
        try {
            $this->db->beginTransaction();
            
            // Получаем данные из старой таблицы
            $sql = "SELECT * FROM maslosklad_buyers WHERE is_deleted = 0";
            $oldCustomers = $this->db->fetchAll($sql);
            
            // Вставляем данные в новую таблицу
            foreach ($oldCustomers as $customer) {
                $this->createCustomer([
                    'name' => $customer['name'],
                    'description' => 'Мигрировано из maslosklad_buyers',
                    'is_internal' => 1 // Предполагаем, что все старые получатели - внутренние
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log('Ошибка миграции покупателей: ' . $e->getMessage());
            return false;
        }
    }
} 