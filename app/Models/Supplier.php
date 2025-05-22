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
                (name, inn, contact_person, phone, email, address, description, is_deleted) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
        $this->db->execute($sql, [
            $data['name'],
            $data['inn'] ?? null,
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
                SET name = ?, inn = ?, contact_person = ?, phone = ?, 
                    email = ?, address = ?, description = ? 
                WHERE id = ?";
        return $this->db->execute($sql, [
            $data['name'],
            $data['inn'] ?? null,
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
     * Поиск поставщиков по названию, контактному лицу, телефону, email или адресу
     *
     * @param string $searchTerm Поисковый запрос
     * @return array
     */
    public function searchSuppliers($searchTerm) {
        if (empty($searchTerm)) {
            return $this->getAllSuppliers();
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
    
    /**
     * Добавление ИНН в таблицу suppliers
     * 
     * @return bool
     */
    public function addInnColumn() {
        try {
            $sql = "ALTER TABLE {$this->table} ADD COLUMN inn VARCHAR(20) DEFAULT NULL AFTER name";
            $this->db->execute($sql);
            return true;
        } catch (\Exception $e) {
            error_log('Ошибка добавления поля ИНН: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Объединение данных поставщиков из старой таблицы в новую с улучшенной логикой
     * 
     * @return array Результаты миграции: количество успешно мигрированных, обновленных, пропущенных записей
     */
    public function mergeSuppliers() {
        $result = [
            'migrated' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0
        ];
        
        try {
            $this->db->beginTransaction();
            
            // Получаем данные из старой таблицы
            $sql = "SELECT * FROM maslosklad_suppliers WHERE is_deleted = 0";
            $oldSuppliers = $this->db->fetchAll($sql);
            
            if (empty($oldSuppliers)) {
                $this->db->commit();
                return $result;
            }
            
            // Получаем существующих поставщиков для проверки дубликатов
            $existingSuppliers = $this->getAllSuppliers();
            $existingNames = [];
            
            foreach ($existingSuppliers as $supplier) {
                $existingNames[mb_strtolower(trim($supplier['name']))] = $supplier['id'];
            }
            
            // Перебираем старых поставщиков и добавляем или обновляем их в новой таблице
            foreach ($oldSuppliers as $oldSupplier) {
                $name = trim($oldSupplier['name']);
                $lowerName = mb_strtolower($name);
                
                // Проверяем, существует ли поставщик с таким именем
                if (isset($existingNames[$lowerName])) {
                    // Поставщик уже существует, обновляем его данные, если нужно
                    $existingId = $existingNames[$lowerName];
                    
                    // Получаем существующего поставщика
                    $existingSupplier = $this->getSupplierById($existingId);
                    
                    // Если поставщик уже имеет контактные данные, не перезаписываем их
                    if (empty($existingSupplier['contact_person']) && empty($existingSupplier['phone']) && 
                        empty($existingSupplier['email']) && empty($existingSupplier['address'])) {
                        // Обновляем только описание, чтобы указать, что это объединенная запись
                        $description = $existingSupplier['description'] ?? '';
                        if ($description) {
                            $description .= '. ';
                        }
                        $description .= 'Объединено из maslosklad_suppliers (ID: ' . $oldSupplier['id'] . ')';
                        
                        $data = [
                            'name' => $existingSupplier['name'],
                            'inn' => $existingSupplier['inn'] ?? null,
                            'contact_person' => $existingSupplier['contact_person'] ?? null,
                            'phone' => $existingSupplier['phone'] ?? null,
                            'email' => $existingSupplier['email'] ?? null,
                            'address' => $existingSupplier['address'] ?? null,
                            'description' => $description
                        ];
                        
                        $this->updateSupplier($existingId, $data);
                        $result['updated']++;
                    } else {
                        $result['skipped']++;
                    }
                } else {
                    // Создаем нового поставщика
                    $data = [
                        'name' => $name,
                        'description' => 'Мигрировано из maslosklad_suppliers (ID: ' . $oldSupplier['id'] . ')'
                    ];
                    
                    $this->createSupplier($data);
                    $result['migrated']++;
                    
                    // Добавляем новое имя в список существующих
                    $newId = $this->db->lastInsertId();
                    $existingNames[$lowerName] = $newId;
                }
            }
            
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log('Ошибка объединения поставщиков: ' . $e->getMessage());
            $result['errors']++;
        }
        
        return $result;
    }
    
    /**
     * Объединение дублирующихся поставщиков
     * 
     * @return array Результаты объединения: количество объединенных, удаленных и пропущенных записей
     */
    public function mergeDuplicates() {
        $result = [
            'merged' => 0,
            'deleted' => 0,
            'skipped' => 0,
            'errors' => 0
        ];
        
        try {
            $this->db->beginTransaction();
            
            // Получаем всех поставщиков
            $suppliers = $this->getAllSuppliers();
            
            if (empty($suppliers)) {
                $this->db->commit();
                return $result;
            }
            
            // Группируем поставщиков по ИНН (если ИНН есть)
            $suppliersWithInn = [];
            $suppliersWithoutInn = [];
            
            foreach ($suppliers as $supplier) {
                if (!empty($supplier['inn'])) {
                    $inn = trim($supplier['inn']);
                    if (!isset($suppliersWithInn[$inn])) {
                        $suppliersWithInn[$inn] = [];
                    }
                    $suppliersWithInn[$inn][] = $supplier;
                } else {
                    $suppliersWithoutInn[] = $supplier;
                }
            }
            
            // Обрабатываем группы поставщиков с одинаковым ИНН
            foreach ($suppliersWithInn as $inn => $suppliers) {
                if (count($suppliers) > 1) {
                    // Выбираем основного поставщика (с наиболее полными данными)
                    $mainSupplier = $this->selectMainSupplier($suppliers);
                    
                    // Обрабатываем остальных поставщиков в группе
                    foreach ($suppliers as $supplier) {
                        if ($supplier['id'] != $mainSupplier['id']) {
                            // Объединяем данные
                            $mainSupplier = $this->mergeSupplierData($mainSupplier, $supplier);
                            
                            // Помечаем дубликат как удаленный
                            $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
                            $this->db->execute($sql, [$supplier['id']]);
                            $result['deleted']++;
                        }
                    }
                    
                    // Сохраняем обновленные данные основного поставщика
                    $this->updateSupplier($mainSupplier['id'], $mainSupplier);
                    $result['merged']++;
                } else {
                    $result['skipped'] += count($suppliers);
                }
            }
            
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log('Ошибка объединения дубликатов поставщиков: ' . $e->getMessage());
            $result['errors']++;
        }
        
        return $result;
    }
    
    /**
     * Выбор основного поставщика из группы дубликатов
     * 
     * @param array $suppliers Массив поставщиков-дубликатов
     * @return array Основной поставщик
     */
    private function selectMainSupplier($suppliers) {
        $mainSupplier = null;
        $maxScore = -1;
        
        foreach ($suppliers as $supplier) {
            $score = 0;
            
            // Оцениваем полноту данных поставщика
            if (!empty($supplier['name'])) $score += 1;
            if (!empty($supplier['inn'])) $score += 2;
            if (!empty($supplier['contact_person'])) $score += 1;
            if (!empty($supplier['phone'])) $score += 1;
            if (!empty($supplier['email'])) $score += 1;
            if (!empty($supplier['address'])) $score += 1;
            if (!empty($supplier['description'])) $score += 1;
            
            // Предпочитаем более новые записи
            $score += strtotime($supplier['created_at']) / 1000000000;
            
            if ($score > $maxScore) {
                $maxScore = $score;
                $mainSupplier = $supplier;
            }
        }
        
        return $mainSupplier;
    }
    
    /**
     * Объединение данных двух поставщиков
     * 
     * @param array $mainSupplier Основной поставщик
     * @param array $duplicateSupplier Дубликат поставщика
     * @return array Обновленные данные основного поставщика
     */
    private function mergeSupplierData($mainSupplier, $duplicateSupplier) {
        // Объединяем описания
        $description = $mainSupplier['description'] ?? '';
        $dupDescription = $duplicateSupplier['description'] ?? '';
        
        if (!empty($dupDescription) && $dupDescription !== $description) {
            if (!empty($description)) {
                $description .= '. ';
            }
            $description .= 'Объединено с ID: ' . $duplicateSupplier['id'] . ' - ' . $dupDescription;
        }
        
        // Берем более полные данные
        $result = [
            'id' => $mainSupplier['id'],
            'name' => !empty($mainSupplier['name']) ? $mainSupplier['name'] : $duplicateSupplier['name'],
            'inn' => !empty($mainSupplier['inn']) ? $mainSupplier['inn'] : $duplicateSupplier['inn'],
            'contact_person' => !empty($mainSupplier['contact_person']) ? $mainSupplier['contact_person'] : $duplicateSupplier['contact_person'],
            'phone' => !empty($mainSupplier['phone']) ? $mainSupplier['phone'] : $duplicateSupplier['phone'],
            'email' => !empty($mainSupplier['email']) ? $mainSupplier['email'] : $duplicateSupplier['email'],
            'address' => !empty($mainSupplier['address']) ? $mainSupplier['address'] : $duplicateSupplier['address'],
            'description' => $description
        ];
        
        return $result;
    }
} 