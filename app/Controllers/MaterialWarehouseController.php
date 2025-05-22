<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\Operation;
use App\Models\Supplier;
use App\Models\Customer;

class MaterialWarehouseController extends Controller {
    protected $itemModel;
    protected $categoryModel;
    protected $warehouseModel;
    protected $inventoryModel;
    protected $operationModel;
    protected $supplierModel;
    protected $customerModel;
    
    public function __construct() {
        parent::__construct();
        $this->itemModel = new Item();
        $this->categoryModel = new Category();
        $this->warehouseModel = new Warehouse();
        $this->inventoryModel = new Inventory();
        $this->operationModel = new Operation();
        $this->supplierModel = new Supplier();
        $this->customerModel = new Customer();
        
        // Добавляем middleware для проверки авторизации
        $this->middleware('auth');
        
        // Добавляем middleware для проверки прав доступа к модулю материального склада
        $this->middleware('permission', ['maslosklad.view']);
    }
    
    /**
     * Главная страница материального склада
     * 
     * @param int $warehouseId ID склада
     */
    public function index($warehouseId) {
        // Проверяем наличие склада
        $warehouse = $this->warehouseModel->getWarehouseById($warehouseId);
        if (!$warehouse) {
            $this->redirect('/maslosklad');
            return;
        }
        
        // Проверяем, что это материальный склад
        if ($warehouse['warehouse_type_name'] !== 'Материальный склад') {
            // Если нет, перенаправляем на главную страницу модуля
            $this->redirect('/maslosklad');
            return;
        }
        
        // Получаем категории материального склада
        $categories = $this->categoryModel->getCategoriesByWarehouseType($warehouse['type_id']);
        
        // Получаем инвентарь на складе
        $inventory = $this->inventoryModel->getWarehouseInventory($warehouseId);
        
        // Группируем товары по категориям
        $inventoryByCategory = [];
        foreach ($inventory as $item) {
            $categoryId = $item['category_id'];
            if (!isset($inventoryByCategory[$categoryId])) {
                $inventoryByCategory[$categoryId] = [];
            }
            $inventoryByCategory[$categoryId][] = $item;
        }
        
        // Получаем статистику операций
        $totalItems = count($inventory);
        $totalReceptions = $this->operationModel->countOperationsByType($warehouseId, Operation::TYPE_RECEPTION);
        $totalIssues = $this->operationModel->countOperationsByType($warehouseId, Operation::TYPE_ISSUE);
        $totalWriteoffs = $this->operationModel->countOperationsByType($warehouseId, Operation::TYPE_WRITEOFF);
        
        // Получаем последние операции для отображения в обзоре
        $recentOperations = $this->operationModel->getRecentOperations($warehouseId, 10);
        
        // Передаем данные в представление
        $this->view->render('maslosklad/material/index', [
            'warehouse' => $warehouse,
            'categories' => $categories,
            'inventoryByCategory' => $inventoryByCategory,
            'totalItems' => $totalItems,
            'totalReceptions' => $totalReceptions,
            'totalIssues' => $totalIssues,
            'totalWriteoffs' => $totalWriteoffs,
            'recentOperations' => $recentOperations,
            'title' => 'Материальный склад: ' . $warehouse['name']
        ]);
    }
    
    /**
     * Страница просмотра товара
     * 
     * @param int $warehouseId ID склада
     * @param int $itemId ID товара
     */
    public function item($warehouseId, $itemId) {
        // Проверяем наличие склада и товара
        $warehouse = $this->warehouseModel->getWarehouseById($warehouseId);
        $item = $this->itemModel->getItemById($itemId);
        
        if (!$warehouse || !$item) {
            $this->redirect('/maslosklad');
            return;
        }
        
        // Получаем инвентарный остаток товара на данном складе
        $inventory = $this->inventoryModel->getInventoryItem($itemId, $warehouseId);
        
        // Получаем историю операций с товаром на данном складе
        $operations = $this->operationModel->getAllOperations([
            'warehouse_id' => $warehouseId,
            'item_id' => $itemId
        ]);
        
        // Получаем свойства товара
        $properties = $this->itemModel->getItemProperties($itemId);
        
        // Передаем данные в представление
        $this->view->render('maslosklad/material/item', [
            'warehouse' => $warehouse,
            'item' => $item,
            'inventory' => $inventory,
            'operations' => $operations,
            'properties' => $properties,
            'title' => 'Товар: ' . $item['name']
        ]);
    }
    
    /**
     * Страница приемки товара
     * 
     * @param int $warehouseId ID склада
     */
    public function reception($warehouseId) {
        // Проверяем наличие прав на создание операций
        $this->middleware('permission', ['maslosklad.create']);
        
        $warehouse = $this->warehouseModel->getWarehouseById($warehouseId);
        if (!$warehouse) {
            $this->redirect('/maslosklad');
            return;
        }
        
        // Получаем категории материального склада
        $categories = $this->categoryModel->getCategoriesByWarehouseType($warehouse['type_id']);
        
        // Получаем список поставщиков для выпадающего списка
        $suppliers = $this->supplierModel->getSuppliersForSelect();
        
        // Отображаем форму приемки товара
        $this->view->render('maslosklad/material/reception', [
            'warehouse' => $warehouse,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'title' => 'Приемка товара: ' . $warehouse['name']
        ]);
    }
    
    /**
     * Обработка AJAX-запроса для приемки товара
     * 
     * @param int $warehouseId ID склада
     */
    public function processReception($warehouseId) {
        // Проверяем наличие прав на создание операций
        $this->middleware('permission', ['maslosklad.create']);
        
        // Проверяем метод запроса
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            return;
        }
        
        // Получаем данные из POST
        $itemId = (int)($_POST['item_id'] ?? 0);
        $quantity = (float)($_POST['quantity'] ?? 0);
        $supplierId = (int)($_POST['supplier_id'] ?? 0);
        $documentNumber = $_POST['document_number'] ?? '';
        $price = (float)($_POST['price'] ?? 0);
        $operationDate = $_POST['operation_date'] ?? date('Y-m-d H:i:s');
        $description = $_POST['description'] ?? '';
        
        // Валидация данных
        if ($itemId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Необходимо выбрать товар']);
            return;
        }
        
        if ($quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Количество должно быть больше нуля']);
            return;
        }
        
        try {
            // Создаем операцию приемки
            $operationData = [
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'operation_type_id' => Operation::TYPE_RECEPTION,
                'quantity' => $quantity,
                'document_number' => $documentNumber,
                'operation_date' => $operationDate,
                'supplier_id' => $supplierId ?: null,
                'price' => $price ?: null,
                'total_cost' => $price * $quantity,
                'description' => $description
            ];
            
            $operationId = $this->operationModel->createOperation($operationData);
            
            if ($operationId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Товар успешно принят на склад',
                    'operation_id' => $operationId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Не удалось создать операцию приемки']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Страница выдачи товара
     * 
     * @param int $warehouseId ID склада
     */
    public function issue($warehouseId) {
        // Проверяем наличие прав на создание операций
        $this->middleware('permission', ['maslosklad.create']);
        
        $warehouse = $this->warehouseModel->getWarehouseById($warehouseId);
        if (!$warehouse) {
            $this->redirect('/maslosklad');
            return;
        }
        
        // Получаем товары, доступные на складе
        $inventory = $this->inventoryModel->getWarehouseInventory($warehouseId);
        
        // Получаем список получателей для выпадающего списка
        $customers = $this->customerModel->getCustomersForSelect();
        
        // Отображаем форму выдачи товара
        $this->view->render('maslosklad/material/issue', [
            'warehouse' => $warehouse,
            'inventory' => $inventory,
            'customers' => $customers,
            'title' => 'Выдача товара: ' . $warehouse['name']
        ]);
    }
    
    /**
     * Обработка AJAX-запроса для выдачи товара
     * 
     * @param int $warehouseId ID склада
     */
    public function processIssue($warehouseId) {
        // Проверяем наличие прав на создание операций
        $this->middleware('permission', ['maslosklad.create']);
        
        // Проверяем метод запроса
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            return;
        }
        
        // Получаем данные из POST
        $itemId = (int)($_POST['item_id'] ?? 0);
        $quantity = (float)($_POST['quantity'] ?? 0);
        $customerId = (int)($_POST['customer_id'] ?? 0);
        $documentNumber = $_POST['document_number'] ?? '';
        $operationDate = $_POST['operation_date'] ?? date('Y-m-d H:i:s');
        $description = $_POST['description'] ?? '';
        
        // Валидация данных
        if ($itemId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Необходимо выбрать товар']);
            return;
        }
        
        if ($quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Количество должно быть больше нуля']);
            return;
        }
        
        // Проверяем наличие товара на складе
        $inventory = $this->inventoryModel->getInventoryItem($itemId, $warehouseId);
        if (!$inventory || $inventory['quantity'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Недостаточное количество товара на складе']);
            return;
        }
        
        try {
            // Создаем операцию выдачи
            $operationData = [
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'operation_type_id' => Operation::TYPE_ISSUE,
                'quantity' => $quantity,
                'document_number' => $documentNumber,
                'operation_date' => $operationDate,
                'customer_id' => $customerId ?: null,
                'description' => $description
            ];
            
            $operationId = $this->operationModel->createOperation($operationData);
            
            if ($operationId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Товар успешно выдан со склада',
                    'operation_id' => $operationId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Не удалось создать операцию выдачи']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Страница списания товара
     * 
     * @param int $warehouseId ID склада
     */
    public function writeoff($warehouseId) {
        // Проверяем наличие прав на создание операций
        $this->middleware('permission', ['maslosklad.create']);
        
        $warehouse = $this->warehouseModel->getWarehouseById($warehouseId);
        if (!$warehouse) {
            $this->redirect('/maslosklad');
            return;
        }
        
        // Получаем товары, доступные на складе
        $inventory = $this->inventoryModel->getWarehouseInventory($warehouseId);
        
        // Отображаем форму списания товара
        $this->view->render('maslosklad/material/writeoff', [
            'warehouse' => $warehouse,
            'inventory' => $inventory,
            'title' => 'Списание товара: ' . $warehouse['name']
        ]);
    }
    
    /**
     * Обработка AJAX-запроса для списания товара
     * 
     * @param int $warehouseId ID склада
     */
    public function processWriteoff($warehouseId) {
        // Проверяем наличие прав на создание операций
        $this->middleware('permission', ['maslosklad.create']);
        
        // Проверяем метод запроса
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            return;
        }
        
        // Получаем данные из POST
        $itemId = (int)($_POST['item_id'] ?? 0);
        $quantity = (float)($_POST['quantity'] ?? 0);
        $reason = $_POST['reason'] ?? '';
        $documentNumber = $_POST['document_number'] ?? '';
        $operationDate = $_POST['operation_date'] ?? date('Y-m-d H:i:s');
        
        // Валидация данных
        if ($itemId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Необходимо выбрать товар']);
            return;
        }
        
        if ($quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Количество должно быть больше нуля']);
            return;
        }
        
        if (empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Необходимо указать причину списания']);
            return;
        }
        
        // Проверяем наличие товара на складе
        $inventory = $this->inventoryModel->getInventoryItem($itemId, $warehouseId);
        if (!$inventory || $inventory['quantity'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Недостаточное количество товара на складе']);
            return;
        }
        
        try {
            // Создаем операцию списания
            $operationData = [
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'operation_type_id' => Operation::TYPE_WRITEOFF,
                'quantity' => $quantity,
                'document_number' => $documentNumber,
                'operation_date' => $operationDate,
                'description' => 'Причина списания: ' . $reason
            ];
            
            $operationId = $this->operationModel->createOperation($operationData);
            
            if ($operationId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Товар успешно списан со склада',
                    'operation_id' => $operationId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Не удалось создать операцию списания']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Страница инвентаризации
     * 
     * @param int $warehouseId ID склада
     */
    public function inventory($warehouseId) {
        // Проверяем наличие прав на управление складом
        $this->middleware('permission', ['maslosklad.manage']);
        
        $warehouse = $this->warehouseModel->getWarehouseById($warehouseId);
        if (!$warehouse) {
            $this->redirect('/maslosklad');
            return;
        }
        
        // Получаем инвентарь на складе
        $inventory = $this->inventoryModel->getWarehouseInventory($warehouseId);
        
        // Группируем товары по категориям
        $categories = $this->categoryModel->getCategoriesByWarehouseType($warehouse['type_id']);
        $categoriesById = [];
        foreach ($categories as $category) {
            $categoriesById[$category['id']] = $category;
        }
        
        // Отображаем форму инвентаризации
        $this->view->render('maslosklad/material/inventory', [
            'warehouse' => $warehouse,
            'inventory' => $inventory,
            'categories' => $categories,
            'categoriesById' => $categoriesById,
            'title' => 'Инвентаризация: ' . $warehouse['name']
        ]);
    }
    
    /**
     * Обработка AJAX-запроса для инвентаризации
     * 
     * @param int $warehouseId ID склада
     */
    public function processInventory($warehouseId) {
        // Проверяем наличие прав на управление складом
        $this->middleware('permission', ['maslosklad.manage']);
        
        // Проверяем метод запроса
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            return;
        }
        
        try {
            // Начинаем транзакцию
            $this->operationModel->db->beginTransaction();
            
            // Обрабатываем данные из формы
            $items = json_decode($_POST['items'] ?? '[]', true);
            $documentNumber = $_POST['document_number'] ?? '';
            $operationDate = $_POST['operation_date'] ?? date('Y-m-d H:i:s');
            $description = $_POST['description'] ?? 'Инвентаризация';
            
            foreach ($items as $item) {
                $itemId = (int)($item['id'] ?? 0);
                $actualQuantity = (float)($item['actual_quantity'] ?? 0);
                
                if ($itemId <= 0) {
                    continue;
                }
                
                // Создаем операцию инвентаризации
                $operationData = [
                    'item_id' => $itemId,
                    'warehouse_id' => $warehouseId,
                    'operation_type_id' => Operation::TYPE_INVENTORY,
                    'quantity' => $actualQuantity,
                    'document_number' => $documentNumber,
                    'operation_date' => $operationDate,
                    'description' => $description
                ];
                
                $this->operationModel->createOperation($operationData);
            }
            
            $this->operationModel->db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Инвентаризация успешно проведена'
            ]);
        } catch (\Exception $e) {
            $this->operationModel->db->rollback();
            echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
} 