<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Operation;
use App\Models\Supplier;
use App\Models\Buyer;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\WarehouseType;

class OperationsController extends Controller {
    /**
     * Список операций с фильтрами и поиском
     */
    public function index() {
        // Получаем фильтры из GET
        $filters = [
            'warehouse_type_id' => $this->getQuery('warehouse_type_id'),
            'search' => $this->getQuery('search'),
            'operation_type' => $this->getQuery('operation_type'),
            'buyer_id' => $this->getQuery('buyer_id'),
            'document_number' => $this->getQuery('document_number'),
        ];
        $opType = $filters['operation_type'];
        if ($opType == 1) {
            $filters['supplier_id'] = $this->getQuery('supplier_id');
        }
        if ($opType == 2 || $opType == 3) {
            $filters['buyer_id'] = $this->getQuery('buyer_id');
        }

        // Получаем справочники для фильтров
        $suppliers = (new Supplier())->getAllSuppliers();
        $buyers = (new Buyer())->getAllBuyers();
        $propertyTypes = (new PropertyType())->getAllTypes();
        $warehouseTypes = (new WarehouseType())->getAllTypes();
        $operationModel = new Operation();
        $documentNumbers = $operationModel->getAllDocumentNumbers();

        // Получаем список операций (пока без фильтрации)
        $operations = $operationModel->getAllOperations($filters);

        $this->view->render('warehouses/operations/index', [
            'operations' => $operations,
            'suppliers' => $suppliers,
            'buyers' => $buyers,
            'propertyTypes' => $propertyTypes,
            'warehouseTypes' => $warehouseTypes,
            'filters' => $filters,
            'documentNumbers' => $documentNumbers,
            'title' => 'Операции'
        ]);
    }

    /**
     * Форма добавления новой операции
     */
    public function create() {
        $suppliers = (new Supplier())->getAllSuppliers();
        $buyers = (new Buyer())->getAllBuyers();
        $propertyTypes = (new PropertyType())->getAllTypes();
        $items = (new \App\Models\Item())->getAllItems();
        $operationTypes = [
            ['id' => Operation::TYPE_RECEPTION, 'name' => 'Приемка'],
            ['id' => Operation::TYPE_ISSUE, 'name' => 'Выдача'],
            ['id' => Operation::TYPE_WRITEOFF, 'name' => 'Списание'],
            ['id' => Operation::TYPE_TRANSFER, 'name' => 'Перемещение'],
            ['id' => Operation::TYPE_BOTTLING, 'name' => 'Розлив'],
            ['id' => Operation::TYPE_INVENTORY, 'name' => 'Инвентаризация'],
        ];
        $this->view->render('warehouses/operations/create', [
            'suppliers' => $suppliers,
            'buyers' => $buyers,
            'propertyTypes' => $propertyTypes,
            'items' => $items,
            'operationTypes' => $operationTypes,
            'title' => 'Добавить операцию'
        ]);
    }

    public function edit($id) {
        $operationModel = new Operation();
        $suppliers = (new Supplier())->getAllSuppliers();
        $buyers = (new Buyer())->getAllBuyers();
        $warehouses = (new \App\Models\Warehouse())->getAllWarehouses();
        $items = (new \App\Models\Item())->getAllItems();
        $operationTypes = [
            ['id' => Operation::TYPE_RECEPTION, 'name' => 'Приемка'],
            ['id' => Operation::TYPE_ISSUE, 'name' => 'Выдача'],
            ['id' => Operation::TYPE_WRITEOFF, 'name' => 'Списание'],
            ['id' => Operation::TYPE_TRANSFER, 'name' => 'Перемещение'],
            ['id' => Operation::TYPE_BOTTLING, 'name' => 'Розлив'],
            ['id' => Operation::TYPE_INVENTORY, 'name' => 'Инвентаризация'],
        ];
        $operation = $operationModel->getOperationById($id);
        if (!$operation) {
            $this->view->render('error/404', ['title' => 'Операция не найдена']);
            return;
        }
        $this->view->render('warehouses/operations/edit', [
            'operation' => $operation,
            'suppliers' => $suppliers,
            'buyers' => $buyers,
            'warehouses' => $warehouses,
            'items' => $items,
            'operationTypes' => $operationTypes,
            'title' => 'Редактировать операцию'
        ]);
    }

    /**
     * Сохранение изменений операции
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            // Приведение типов и обработка пустых значений
            $data['quantity'] = isset($data['quantity']) ? (int)$data['quantity'] : null;
            $data['volume'] = isset($data['volume']) ? (float)$data['volume'] : null;
            $data['operation_date'] = isset($data['operation_date']) ? $data['operation_date'] : date('Y-m-d H:i:s');
            $data['supplier_id'] = !empty($data['supplier_id']) ? (int)$data['supplier_id'] : null;
            $data['buyer_id'] = !empty($data['buyer_id']) ? (int)$data['buyer_id'] : null;
            $data['warehouse_id'] = !empty($data['warehouse_id']) ? (int)$data['warehouse_id'] : null;
            $data['warehouse_id_to'] = !empty($data['warehouse_id_to']) ? (int)$data['warehouse_id_to'] : null;
            $data['description'] = $data['description'] ?? '';
            $data['item_id'] = (int)($data['item_id'] ?? 0);
            $data['operation_type_id'] = (int)($data['operation_type_id'] ?? 0);

            $operationModel = new Operation();
            $result = $operationModel->updateOperation($id, $data);
            if ($result) {
                $_SESSION['success'] = 'Операция успешно обновлена';
                header('Location: /warehouses/operations');
                exit;
            } else {
                $_SESSION['error'] = 'Ошибка при обновлении операции';
                header('Location: /warehouses/operations/edit/' . $id);
                exit;
            }
        } else {
            $_SESSION['error'] = 'Некорректный метод запроса';
            header('Location: /warehouses/operations');
            exit;
        }
    }

    /**
     * Сохранение новой операции
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            // Приведение типов и обработка пустых значений
            $data['quantity'] = isset($data['quantity']) ? (int)$data['quantity'] : null;
            $data['volume'] = isset($data['volume']) ? (float)$data['volume'] : null;
            $data['operation_date'] = isset($data['operation_date']) ? $data['operation_date'] : date('Y-m-d H:i:s');
            $data['supplier_id'] = !empty($data['supplier_id']) ? (int)$data['supplier_id'] : null;
            $data['buyer_id'] = !empty($data['buyer_id']) ? (int)$data['buyer_id'] : null;
            $data['warehouse_id'] = !empty($data['warehouse_id']) ? (int)$data['warehouse_id'] : null;
            $data['warehouse_id_to'] = !empty($data['warehouse_id_to']) ? (int)$data['warehouse_id_to'] : null;
            $data['description'] = $data['description'] ?? '';
            $data['item_id'] = (int)($data['item_id'] ?? 0);
            $data['operation_type_id'] = (int)($data['operation_type_id'] ?? 0);
            $data['document_number'] = $data['document_number'] ?? null;

            $operationModel = new Operation();
            $result = $operationModel->createOperation($data);
            if ($result) {
                $_SESSION['success'] = 'Операция успешно добавлена';
                header('Location: /warehouses/operations');
                exit;
            } else {
                $_SESSION['error'] = 'Ошибка при добавлении операции';
                header('Location: /warehouses/operations/create');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Некорректный метод запроса';
            header('Location: /warehouses/operations');
            exit;
        }
    }
} 