<?php

namespace App\Models;

use App\Core\Model;

class Operation extends Model {
    protected $table = 'operations';
    
    // Константы типов операций
    const TYPE_RECEPTION = 1;  // Приемка
    const TYPE_ISSUE = 2;      // Выдача
    const TYPE_WRITEOFF = 3;   // Списание
    const TYPE_TRANSFER = 4;   // Перемещение
    const TYPE_BOTTLING = 5;   // Розлив (для ГСМ)
    const TYPE_INVENTORY = 6;  // Инвентаризация
    
    /**
     * Получение всех операций
     *
     * @param array $filters Фильтры (warehouse_id, item_id, operation_type, date_from, date_to)
     * @return array
     */
    public function getAllOperations($filters = []) {
        $sql = "SELECT o.*, 
                    i.name as item_name, 
                    w.name as warehouse_name,
                    ot.name as operation_type_name,
                    s.name as supplier_name,
                    c.name as buyer_name
                FROM {$this->table} o
                JOIN items i ON o.item_id = i.id
                JOIN warehouses w ON o.warehouse_id = w.id
                JOIN operation_types ot ON o.operation_type_id = ot.id
                LEFT JOIN suppliers s ON o.supplier_id = s.id
                LEFT JOIN buyers c ON o.buyer_id = c.id
                LEFT JOIN items_categories cat ON i.category_id = cat.id
                WHERE o.is_deleted = 0";
        
        $params = [];
        
        // Применение фильтров
        if (!empty($filters['warehouse_id'])) {
            $sql .= " AND o.warehouse_id = ?";
            $params[] = $filters['warehouse_id'];
        }
        
        if (!empty($filters['item_id'])) {
            $sql .= " AND o.item_id = ?";
            $params[] = $filters['item_id'];
        }
        
        if (!empty($filters['operation_type'])) {
            $sql .= " AND o.operation_type_id = ?";
            $params[] = $filters['operation_type'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND o.operation_date >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND o.operation_date <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['warehouse_type_id'])) {
            $sql .= " AND cat.warehouse_type_id = ?";
            $params[] = $filters['warehouse_type_id'];
        }
        
        $sql .= " ORDER BY o.operation_date DESC, o.id DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Получение операции по ID
     *
     * @param int $id
     * @return array|false
     */
    public function getOperationById($id) {
        $sql = "SELECT o.*, 
                    i.name as item_name, 
                    wt.name as warehouse_type_name,
                    w.name as warehouse_name,
                    ot.name as operation_type_name,
                    s.name as supplier_name,
                    c.name as buyer_name
                FROM {$this->table} o
                JOIN items i ON o.item_id = i.id
                JOIN warehouses w ON o.warehouse_id = w.id
                JOIN warehouse_types wt ON w.type_id = wt.id
                JOIN operation_types ot ON o.operation_type_id = ot.id
                LEFT JOIN suppliers s ON o.supplier_id = s.id
                LEFT JOIN buyers c ON o.buyer_id = c.id
                WHERE o.id = ? AND o.is_deleted = 0";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Создание новой операции
     *
     * @param array $data
     * @return int ID созданной операции
     */
    public function createOperation($data) {
        $this->db->beginTransaction();
        
        try {
            $sql = "INSERT INTO {$this->table} 
                    (item_id, warehouse_id, operation_type_id, quantity, volume, 
                    document_number, operation_date, supplier_id, buyer_id, 
                    price, total_cost, description, is_deleted) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
            
            $this->db->execute($sql, [
                $data['item_id'],
                $data['warehouse_id'],
                $data['operation_type_id'],
                $data['quantity'],
                $data['volume'] ?? null,
                $data['document_number'] ?? null,
                $data['operation_date'] ?? date('Y-m-d H:i:s'),
                $data['supplier_id'] ?? null,
                $data['buyer_id'] ?? null,
                $data['price'] ?? null,
                $data['total_cost'] ?? null,
                $data['description'] ?? null
            ]);
            
            $operationId = $this->db->lastInsertId();
            
            // Обновляем инвентарный остаток
            $this->updateInventory(
                $data['item_id'], 
                $data['warehouse_id'], 
                $data['operation_type_id'], 
                $data['quantity'], 
                $data['volume'] ?? null
            );
            
            $this->db->commit();
            $this->logInventory('Операция создана', ['operation_id' => $operationId]);
            return $operationId;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log('Ошибка создания операции: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Обновление операции
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateOperation($id, $data) {
        $this->db->beginTransaction();
        
        try {
            // Получаем текущую операцию для отмены изменений в инвентаре
            $currentOperation = $this->getOperationById($id);
            
            if (!$currentOperation) {
                throw new \Exception("Операция с ID {$id} не найдена");
            }
            
            // Отменяем влияние текущей операции на инвентарь
            $this->reverseInventoryChange(
                $currentOperation['item_id'],
                $currentOperation['warehouse_id'],
                $currentOperation['operation_type_id'],
                $currentOperation['quantity'],
                $currentOperation['volume']
            );
            
            // Обновляем операцию
            $sql = "UPDATE {$this->table} 
                    SET item_id = ?, warehouse_id = ?, operation_type_id = ?, 
                        quantity = ?, volume = ?, document_number = ?, 
                        operation_date = ?, supplier_id = ?, buyer_id = ?, 
                        price = ?, total_cost = ?, description = ? 
                    WHERE id = ?";
            
            $this->db->execute($sql, [
                $data['item_id'],
                $data['warehouse_id'],
                $data['operation_type_id'],
                $data['quantity'],
                $data['volume'] ?? null,
                $data['document_number'] ?? null,
                $data['operation_date'] ?? date('Y-m-d H:i:s'),
                $data['supplier_id'] ?? null,
                $data['buyer_id'] ?? null,
                $data['price'] ?? null,
                $data['total_cost'] ?? null,
                $data['description'] ?? null,
                $id
            ]);
            
            // Применяем новое влияние на инвентарь
            $this->updateInventory(
                $data['item_id'], 
                $data['warehouse_id'], 
                $data['operation_type_id'], 
                $data['quantity'], 
                $data['volume'] ?? null
            );
            
            $this->db->commit();
            $this->logInventory('Операция обновлена', ['operation_id' => $id]);
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log('Ошибка обновления операции: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Удаление операции (мягкое удаление)
     *
     * @param int $id
     * @return bool
     */
    public function deleteOperation($id) {
        $this->db->beginTransaction();
        
        try {
            // Получаем текущую операцию для отмены изменений в инвентаре
            $currentOperation = $this->getOperationById($id);
            
            if (!$currentOperation) {
                throw new \Exception("Операция с ID {$id} не найдена");
            }
            
            // Отменяем влияние операции на инвентарь
            $this->reverseInventoryChange(
                $currentOperation['item_id'],
                $currentOperation['warehouse_id'],
                $currentOperation['operation_type_id'],
                $currentOperation['quantity'],
                $currentOperation['volume']
            );
            
            // Помечаем операцию как удаленную
            $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
            $this->db->execute($sql, [$id]);
            
            $this->db->commit();
            $this->logInventory('Операция удалена', ['operation_id' => $id]);
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log('Ошибка удаления операции: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Обновление инвентарных остатков на основе операции
     *
     * @param int $itemId
     * @param int $warehouseId
     * @param int $operationTypeId
     * @param float $quantity
     * @param float|null $volume
     * @return void
     */
    protected function updateInventory($itemId, $warehouseId, $operationTypeId, $quantity, $volume = null) {
        // Получаем текущий остаток
        $inventory = $this->getInventoryItem($itemId, $warehouseId);

        // Получаем тип товара (наливной или штучный)
        $itemModel = new \App\Models\Item();
        $item = $itemModel->getItemById($itemId);
        $hasVolume = !empty($item['has_volume']);

        // Рассчитываем новые значения в зависимости от типа операции и типа товара
        switch ($operationTypeId) {
            case self::TYPE_RECEPTION:
                if ($hasVolume) {
                    // Наливной товар — увеличиваем только объем
                    $newQuantity = $inventory ? $inventory['quantity'] : 0;
                    $newVolume = ($inventory && $inventory['volume'] ? $inventory['volume'] : 0) + ($volume ?? 0);
                } else {
                    // Штучный товар — увеличиваем только количество
                    $newQuantity = ($inventory ? $inventory['quantity'] : 0) + $quantity;
                    $newVolume = $inventory && $inventory['volume'] ? $inventory['volume'] : 0;
                }
                break;
            case self::TYPE_ISSUE:
            case self::TYPE_WRITEOFF:
                if ($hasVolume) {
                    $newQuantity = $inventory ? $inventory['quantity'] : 0;
                    $newVolume = ($inventory && $inventory['volume'] ? $inventory['volume'] : 0) - ($volume ?? 0);
                    if ($newVolume < 0) {
                        throw new \Exception("Недостаточно объема на складе");
                    }
                } else {
                    $newQuantity = ($inventory ? $inventory['quantity'] : 0) - $quantity;
                    $newVolume = $inventory && $inventory['volume'] ? $inventory['volume'] : 0;
                    if ($newQuantity < 0) {
                        throw new \Exception("Недостаточно товара на складе");
                    }
                }
                break;
            case self::TYPE_BOTTLING:
                // Розлив — уменьшаем объем у исходного товара (например, бочка), увеличиваем у целевого (если реализовано)
                if ($hasVolume) {
                    $newQuantity = $inventory ? $inventory['quantity'] : 0;
                    $newVolume = ($inventory && $inventory['volume'] ? $inventory['volume'] : 0) - ($volume ?? 0);
                    if ($newVolume < 0) {
                        throw new \Exception("Недостаточно объема для розлива");
                    }
                } else {
                    // Для штучных товаров розлив не применяется
                    throw new \Exception("Операция розлива применима только к наливным товарам");
                }
                break;
            case self::TYPE_INVENTORY:
                if ($hasVolume) {
                    $newQuantity = $inventory ? $inventory['quantity'] : 0;
                    $newVolume = $volume ?? 0;
                } else {
                    $newQuantity = $quantity;
                    $newVolume = $inventory && $inventory['volume'] ? $inventory['volume'] : 0;
                }
                break;
            default:
                throw new \Exception("Неизвестный тип операции: {$operationTypeId}");
        }

        // Сохраняем или обновляем инвентарный остаток
        if ($inventory) {
            $this->updateInventoryItem($itemId, $warehouseId, $newQuantity, $newVolume);
        } else {
            $this->createInventoryItem($itemId, $warehouseId, $newQuantity, $newVolume);
        }
        $this->logInventory('Инвентарный остаток обновлен', ['item_id' => $itemId, 'warehouse_id' => $warehouseId, 'quantity' => $newQuantity, 'volume' => $newVolume]);
    }
    
    /**
     * Отменяет влияние операции на инвентарь (для обновления/удаления)
     *
     * @param int $itemId
     * @param int $warehouseId
     * @param int $operationTypeId
     * @param float $quantity
     * @param float|null $volume
     * @return void
     */
    protected function reverseInventoryChange($itemId, $warehouseId, $operationTypeId, $quantity, $volume = null) {
        switch ($operationTypeId) {
            case self::TYPE_RECEPTION:
                // Для приемки отменяем увеличение (уменьшаем)
                $this->updateInventory($itemId, $warehouseId, self::TYPE_ISSUE, $quantity, $volume);
                break;
                
            case self::TYPE_ISSUE:
            case self::TYPE_WRITEOFF:
                // Для выдачи/списания отменяем уменьшение (увеличиваем)
                $this->updateInventory($itemId, $warehouseId, self::TYPE_RECEPTION, $quantity, $volume);
                break;
                
            case self::TYPE_INVENTORY:
                // Для инвентаризации нужно вернуть предыдущее значение
                // Поэтому просто получаем текущее значение и ничего не делаем
                // Новая операция установит новое значение
                break;
                
            default:
                // Для других типов операций нужна специфичная логика
                break;
        }
        $this->logInventory('Влияние операции отменено', ['item_id' => $itemId, 'warehouse_id' => $warehouseId, 'operation_type_id' => $operationTypeId, 'quantity' => $quantity, 'volume' => $volume]);
    }
    
    /**
     * Получение инвентарного остатка для товара на складе
     *
     * @param int $itemId
     * @param int $warehouseId
     * @return array|false
     */
    protected function getInventoryItem($itemId, $warehouseId) {
        $sql = "SELECT * FROM inventory WHERE item_id = ? AND warehouse_id = ?";
        return $this->db->fetch($sql, [$itemId, $warehouseId]);
    }
    
    /**
     * Создание инвентарного остатка
     *
     * @param int $itemId
     * @param int $warehouseId
     * @param float $quantity
     * @param float|null $volume
     * @return int
     */
    protected function createInventoryItem($itemId, $warehouseId, $quantity, $volume = null) {
        $sql = "INSERT INTO inventory (item_id, warehouse_id, quantity, volume, last_update) 
                VALUES (?, ?, ?, ?, NOW())";
        $this->db->execute($sql, [$itemId, $warehouseId, $quantity, $volume]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Обновление инвентарного остатка
     *
     * @param int $itemId
     * @param int $warehouseId
     * @param float $quantity
     * @param float|null $volume
     * @return bool
     */
    protected function updateInventoryItem($itemId, $warehouseId, $quantity, $volume = null) {
        $sql = "UPDATE inventory 
                SET quantity = ?, volume = ?, last_update = NOW() 
                WHERE item_id = ? AND warehouse_id = ?";
        return $this->db->execute($sql, [$quantity, $volume, $itemId, $warehouseId]) > 0;
    }

    /**
     * Подсчет количества операций по типу
     *
     * @param int $warehouseId ID склада
     * @param int $operationType ID типа операции
     * @return int
     */
    public function countOperationsByType($warehouseId, $operationType) {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE warehouse_id = ? 
                AND operation_type_id = ? 
                AND is_deleted = 0";
        
        $result = $this->db->fetch($sql, [$warehouseId, $operationType]);
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Получение последних операций для склада
     *
     * @param int $warehouseId ID склада
     * @param int $limit Максимальное количество операций
     * @return array
     */
    public function getRecentOperations($warehouseId, $limit = 10) {
        $sql = "SELECT o.*, 
                    i.name as item_name,
                    i.unit,
                    ot.name as operation_type_name,
                    u.username
                FROM {$this->table} o
                JOIN items i ON o.item_id = i.id
                JOIN operation_types ot ON o.operation_type_id = ot.id
                LEFT JOIN users u ON o.created_by = u.id
                WHERE o.warehouse_id = ? 
                AND o.is_deleted = 0
                ORDER BY o.created_at DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$warehouseId, $limit]);
    }

    protected function logInventory($message, $context = []) {
        $log = date('[Y-m-d H:i:s] ') . $message;
        if (!empty($context)) {
            $log .= ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        // Определяем путь до папки logs относительно корня проекта
        $logDir = realpath(__DIR__ . '/../../../logs');
        if ($logDir === false) {
            // Если папка не найдена, пробуем создать относительно DOCUMENT_ROOT
            $logDir = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] . '/logs' : __DIR__ . '/../../../logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
        }
        $logFile = $logDir . '/inventory.log';
        file_put_contents($logFile, $log . PHP_EOL, FILE_APPEND);
    }

    public function recalcInventoryForItemWarehouse($itemId, $warehouseId) {
        $sql = "SELECT operation_type_id, quantity, volume FROM {$this->table} WHERE item_id = ? AND warehouse_id = ? AND is_deleted = 0 ORDER BY operation_date, id";
        $rows = $this->db->fetchAll($sql, [$itemId, $warehouseId]);
        $itemModel = new \App\Models\Item();
        $item = $itemModel->getItemById($itemId);
        $hasVolume = !empty($item['has_volume']);
        $quantity = 0;
        $volume = 0;
        foreach ($rows as $row) {
            switch ($row['operation_type_id']) {
                case self::TYPE_RECEPTION:
                    if ($hasVolume) {
                        $volume += $row['volume'] ?? 0;
                    } else {
                        $quantity += $row['quantity'];
                    }
                    break;
                case self::TYPE_ISSUE:
                case self::TYPE_WRITEOFF:
                    if ($hasVolume) {
                        $volume -= $row['volume'] ?? 0;
                    } else {
                        $quantity -= $row['quantity'];
                    }
                    break;
                case self::TYPE_INVENTORY:
                    if ($hasVolume) {
                        $volume = $row['volume'] ?? 0;
                    } else {
                        $quantity = $row['quantity'];
                    }
                    break;
            }
        }
        // Обновляем или создаём запись в inventory
        $inventory = $this->getInventoryItem($itemId, $warehouseId);
        if ($inventory) {
            $this->updateInventoryItem($itemId, $warehouseId, $quantity, $hasVolume ? $volume : null);
        } else if ($quantity != 0 || $volume != 0) {
            $this->createInventoryItem($itemId, $warehouseId, $quantity, $hasVolume ? $volume : null);
        }
        $this->logInventory('Пересчёт остатков', ['item_id' => $itemId, 'warehouse_id' => $warehouseId, 'quantity' => $quantity, 'volume' => $volume]);
    }
} 