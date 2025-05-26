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
            'supplier_id' => $this->getQuery('supplier_id'),
            'buyer_id' => $this->getQuery('buyer_id'),
            'property_type_id' => $this->getQuery('property_type_id'),
            'warehouse_type_id' => $this->getQuery('warehouse_type_id'),
            'date_from' => $this->getQuery('date_from'),
            'date_to' => $this->getQuery('date_to'),
            'search' => $this->getQuery('search'),
            'operation_type' => $this->getQuery('operation_type'),
        ];

        // Получаем справочники для фильтров
        $suppliers = (new Supplier())->getAllSuppliers();
        $buyers = (new Buyer())->getAllBuyers();
        $propertyTypes = (new PropertyType())->getAllTypes();
        $warehouseTypes = (new WarehouseType())->getAllTypes();

        // Получаем список операций (пока без фильтрации)
        $operations = (new Operation())->getAllOperations($filters);

        $this->view->render('warehouses/operations/index', [
            'operations' => $operations,
            'suppliers' => $suppliers,
            'buyers' => $buyers,
            'propertyTypes' => $propertyTypes,
            'warehouseTypes' => $warehouseTypes,
            'filters' => $filters,
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
} 