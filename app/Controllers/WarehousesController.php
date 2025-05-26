<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\WarehouseType;
use App\Models\Warehouse;

class WarehousesController extends Controller {
    protected $warehouseTypeModel;
    protected $warehouseModel;
    
    public function __construct() {
        parent::__construct();
        
        $this->warehouseTypeModel = new WarehouseType();
        $this->warehouseModel = new Warehouse();
        
        // Добавляем middleware для проверки авторизации
        $this->middleware('auth');
        
        // Добавляем middleware для проверки прав доступа к модулю складов
        $this->middleware('permission', ['maslosklad.view']);
    }
    
    /**
     * Главная страница модуля управления складами
     */
    public function index() {
        try {
            // Получаем все склады
            $warehouses = $this->warehouseModel->getAllWarehouses();
            
            // Группировка по типу больше не нужна
            // Получаем права пользователя для отображения в шаблоне
            $userPermissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
            // Передаем данные в представление через шаблонизатор
            $this->view->render('warehouses/index', [
                'warehouses' => $warehouses,
                'title' => 'Система управления складами',
                'userPermissions' => $userPermissions
            ]);
            
        } catch (\Exception $e) {
            // Если произошла ошибка, отображаем ее через стандартный шаблон ошибки
            $this->view->render('error/404', [
                'title' => 'Ошибка',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Отображение информации о складе
     * 
     * @param int $id ID склада
     */
    public function warehouse($id) {
        // Проверяем наличие склада
        $warehouse = $this->warehouseModel->getWarehouseById($id);
        if (!$warehouse) {
            $this->redirect('/warehouses');
            return;
        }
        
        // Просто отображаем склад
        $this->view->render('warehouses/warehouse', [
            'warehouse' => $warehouse,
            'title' => 'Склад: ' . $warehouse['name']
        ]);
    }
    
    /**
     * Страница управления складами
     */
    public function warehouses() {
        // Проверяем наличие прав на управление складами
        $this->middleware('permission', ['maslosklad.manage']);
        
        // Получаем все типы складов
        $warehouseTypes = $this->warehouseTypeModel->getAllTypes();
        
        // Получаем все склады
        $warehouses = $this->warehouseModel->getAllWarehouses();
        
        // Передаем данные в представление
        $this->view->render('warehouses/manage', [
            'warehouseTypes' => $warehouseTypes,
            'warehouses' => $warehouses,
            'title' => 'Управление складами'
        ]);
    }
    
    /**
     * Обработка AJAX-запроса для создания нового склада
     */
    public function createWarehouse() {
        // Проверяем наличие прав на управление складами
        $this->middleware('permission', ['maslosklad.manage']);
        
        // Проверяем, что запрос отправлен методом POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            return;
        }
        
        // Получаем данные из POST
        $data = [
            'name' => $_POST['name'] ?? '',
            'location' => $_POST['location'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        
        // Валидация данных
        if (empty($data['name'])) {
            echo json_encode(['success' => false, 'message' => 'Название склада не может быть пустым']);
            return;
        }
        
        try {
            // Создаем новый склад
            $warehouseId = $this->warehouseModel->createWarehouse($data);
            
            if ($warehouseId) {
                $warehouse = $this->warehouseModel->getWarehouseById($warehouseId);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Склад успешно создан',
                    'warehouse' => $warehouse
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Не удалось создать склад']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Обработка AJAX-запроса для обновления данных склада
     * 
     * @param int $id ID склада
     */
    public function updateWarehouse($id) {
        // Проверяем наличие прав на управление складами
        $this->middleware('permission', ['maslosklad.manage']);
        
        // Проверяем, что запрос отправлен методом POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            return;
        }
        
        // Проверяем наличие склада
        $warehouse = $this->warehouseModel->getWarehouseById($id);
        if (!$warehouse) {
            echo json_encode(['success' => false, 'message' => 'Склад не найден']);
            return;
        }
        
        // Получаем данные из POST
        $data = [
            'name' => $_POST['name'] ?? '',
            'location' => $_POST['location'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        
        // Валидация данных
        if (empty($data['name'])) {
            echo json_encode(['success' => false, 'message' => 'Название склада не может быть пустым']);
            return;
        }
        
        try {
            // Обновляем данные склада
            $result = $this->warehouseModel->updateWarehouse($id, $data);
            
            if ($result) {
                $updatedWarehouse = $this->warehouseModel->getWarehouseById($id);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Данные склада успешно обновлены',
                    'warehouse' => $updatedWarehouse
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Не удалось обновить данные склада']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Обработка AJAX-запроса для удаления склада
     * 
     * @param int $id ID склада
     */
    public function deleteWarehouse($id) {
        // Проверяем наличие прав на управление складами
        $this->middleware('permission', ['maslosklad.manage']);
        
        // Проверяем, что запрос отправлен методом POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            return;
        }
        
        // Проверяем наличие склада
        $warehouse = $this->warehouseModel->getWarehouseById($id);
        if (!$warehouse) {
            echo json_encode(['success' => false, 'message' => 'Склад не найден']);
            return;
        }
        
        try {
            // Удаляем склад
            $result = $this->warehouseModel->deleteWarehouse($id);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Склад успешно удален'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Не удалось удалить склад']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Отображает материальные склады
     */
    public function material() {
        try {
            // Получаем тип склада
            $warehouseType = $this->warehouseTypeModel->getTypeByCode('material');
            if (!$warehouseType) {
                $this->redirect('/warehouses');
                return;
            }
            
            // Получаем все склады данного типа
            $warehouses = $this->warehouseModel->getWarehousesByType($warehouseType['id']);
            
            // Группируем склады по типам для формата представления
            $warehousesByType = [
                $warehouseType['id'] => $warehouses
            ];
            
            // Получаем права пользователя для отображения в шаблоне
            $userPermissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
            
            // Передаем данные в представление
            $this->view->render('warehouses/index', [
                'warehouseTypes' => [$warehouseType],
                'warehousesByType' => $warehousesByType,
                'title' => 'Материальные склады',
                'userPermissions' => $userPermissions
            ]);
        } catch (\Exception $e) {
            $this->view->render('error/404', [
                'title' => 'Ошибка',
                'message' => $e->getMessage()
            ]);
            $this->redirect('/warehouses');
        }
    }
    
    /**
     * Отображает инструментальные склады
     */
    public function tool() {
        try {
            // Получаем тип склада
            $warehouseType = $this->warehouseTypeModel->getTypeByCode('tool');
            if (!$warehouseType) {
                $this->redirect('/warehouses');
                return;
            }
            
            // Получаем все склады данного типа
            $warehouses = $this->warehouseModel->getWarehousesByType($warehouseType['id']);
            
            // Группируем склады по типам для формата представления
            $warehousesByType = [
                $warehouseType['id'] => $warehouses
            ];
            
            // Получаем права пользователя для отображения в шаблоне
            $userPermissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
            
            // Передаем данные в представление
            $this->view->render('warehouses/index', [
                'warehouseTypes' => [$warehouseType],
                'warehousesByType' => $warehousesByType,
                'title' => 'Инструментальные склады',
                'userPermissions' => $userPermissions
            ]);
        } catch (\Exception $e) {
            $this->view->render('error/404', [
                'title' => 'Ошибка',
                'message' => $e->getMessage()
            ]);
            $this->redirect('/warehouses');
        }
    }
    
    /**
     * Отображает склады ГСМ
     */
    public function oil() {
        try {
            // Получаем тип склада
            $warehouseType = $this->warehouseTypeModel->getTypeByCode('oil');
            if (!$warehouseType) {
                $this->redirect('/warehouses');
                return;
            }
            
            // Получаем все склады данного типа
            $warehouses = $this->warehouseModel->getWarehousesByType($warehouseType['id']);
            
            // Группируем склады по типам для формата представления
            $warehousesByType = [
                $warehouseType['id'] => $warehouses
            ];
            
            // Получаем права пользователя для отображения в шаблоне
            $userPermissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
            
            // Передаем данные в представление
            $this->view->render('warehouses/index', [
                'warehouseTypes' => [$warehouseType],
                'warehousesByType' => $warehousesByType,
                'title' => 'Склады ГСМ',
                'userPermissions' => $userPermissions
            ]);
        } catch (\Exception $e) {
            $this->view->render('error/404', [
                'title' => 'Ошибка',
                'message' => $e->getMessage()
            ]);
            $this->redirect('/warehouses');
        }
    }
    
    /**
     * Отображает склады автозапчастей
     */
    public function autoparts() {
        try {
            // Получаем тип склада
            $warehouseType = $this->warehouseTypeModel->getTypeByCode('autoparts');
            if (!$warehouseType) {
                $this->redirect('/warehouses');
                return;
            }
            
            // Получаем все склады данного типа
            $warehouses = $this->warehouseModel->getWarehousesByType($warehouseType['id']);
            
            // Группируем склады по типам для формата представления
            $warehousesByType = [
                $warehouseType['id'] => $warehouses
            ];
            
            // Получаем права пользователя для отображения в шаблоне
            $userPermissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
            
            // Передаем данные в представление
            $this->view->render('warehouses/index', [
                'warehouseTypes' => [$warehouseType],
                'warehousesByType' => $warehousesByType,
                'title' => 'Склады автозапчастей',
                'userPermissions' => $userPermissions
            ]);
        } catch (\Exception $e) {
            $this->view->render('error/404', [
                'title' => 'Ошибка',
                'message' => $e->getMessage()
            ]);
            $this->redirect('/warehouses');
        }
    }
    
    /**
     * Страница Поставщики
     */
    public function suppliers() {
        // Проверяем наличие прав на просмотр поставщиков
        $this->middleware('permission', ['maslosklad.view']);
        
        // Подключаем модель поставщиков
        $supplierModel = new \App\Models\Supplier();
        
        // Добавляем поле ИНН в таблицу suppliers, если его нет
        $supplierModel->addInnColumn();
        
        // Получаем всех поставщиков
        $suppliers = $supplierModel->getAllSuppliers();
        
        // Передаем данные в представление
        $this->view->render('warehouses/suppliers/index', [
            'suppliers' => $suppliers,
            'title' => 'Поставщики',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }
    
    /**
     * Обновление структуры таблицы поставщиков
     */
    public function updateSupplierTable() {
        // Проверяем наличие прав на управление
        $this->middleware('permission', ['maslosklad.manage']);
        
        $supplierModel = new \App\Models\Supplier();
        $result = $supplierModel->addInnColumn();
        
        if ($result) {
            $_SESSION['success'] = 'Таблица поставщиков успешно обновлена. Добавлен ИНН.';
        } else {
            $_SESSION['error'] = 'Ошибка при обновлении таблицы поставщиков';
        }
        
        $this->redirect('/warehouses/suppliers');
    }
    
    /**
     * Страница Получатели
     */
    public function customers() {
        // Проверяем наличие прав на просмотр получателей
        $this->middleware('permission', ['maslosklad.view']);
        
        // Подключаем модель получателей
        $customerModel = new \App\Models\Customer();
        
        // Получаем всех получателей
        $customers = $customerModel->getAllCustomers();
        
        // Передаем данные в представление
        $this->view->render('warehouses/customers/index', [
            'customers' => $customers,
            'title' => 'Получатели',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }
    
    /**
     * Страница Операции
     */
    public function operations() {
        // Проверяем наличие прав на просмотр операций
        $this->middleware('permission', ['maslosklad.view']);
        
        // Подключаем модель операций
        $operationModel = new \App\Models\Operation();
        
        // Получаем параметры фильтрации из GET-запроса
        $filters = [
            'warehouse_id' => isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : null,
            'item_id' => isset($_GET['item_id']) ? (int)$_GET['item_id'] : null,
            'operation_type' => isset($_GET['operation_type']) ? (int)$_GET['operation_type'] : null,
            'date_from' => isset($_GET['date_from']) ? $_GET['date_from'] : null,
            'date_to' => isset($_GET['date_to']) ? $_GET['date_to'] : null
        ];
        
        // Получаем все операции с применением фильтров
        $operations = $operationModel->getAllOperations($filters);
        
        // Получаем типы складов и склады для фильтра
        $warehouseTypeModel = new \App\Models\WarehouseType();
        $warehouseModel = new \App\Models\Warehouse();
        
        $warehouseTypes = $warehouseTypeModel->getAllTypes();
        $warehouses = $warehouseModel->getAllWarehouses();
        
        // Передаем данные в представление
        $this->view->render('warehouses/operations/index', [
            'operations' => $operations,
            'warehouseTypes' => $warehouseTypes,
            'warehouses' => $warehouses,
            'filters' => $filters,
            'title' => 'Журнал операций',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }
    
    /**
     * Страница Отчеты
     */
    public function reports() {
        // Проверяем наличие прав на просмотр отчетов
        $this->middleware('permission', ['maslosklad.view']);
        
        // Получаем типы отчетов
        $reportTypes = [
            'inventory' => 'Текущие остатки',
            'movements' => 'Движение товаров',
            'receipts' => 'Отчет по приёмкам',
            'issues' => 'Отчет по выдачам',
            'writeoffs' => 'Отчет по списаниям'
        ];
        
        // Получаем параметры для отчета из GET-запроса
        $reportType = isset($_GET['type']) ? $_GET['type'] : null;
        
        // Создаем переменную для данных отчета
        $reportData = [];
        
        // Если выбран тип отчета, формируем отчет
        if ($reportType) {
            // В зависимости от типа отчета, вызываем соответствующий метод
            switch ($reportType) {
                case 'inventory':
                    $reportData = $this->generateInventoryReport();
                    break;
                case 'movements':
                    $reportData = $this->generateMovementsReport();
                    break;
                case 'receipts':
                    $reportData = $this->generateReceiptsReport();
                    break;
                case 'issues':
                    $reportData = $this->generateIssuesReport();
                    break;
                case 'writeoffs':
                    $reportData = $this->generateWriteoffsReport();
                    break;
            }
        }
        
        // Передаем данные в представление
        $this->view->render('warehouses/reports/index', [
            'reportTypes' => $reportTypes,
            'selectedReportType' => $reportType,
            'reportData' => $reportData,
            'title' => 'Отчеты',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }
    
    /**
     * Страница Статистика
     */
    public function statistics() {
        // Здесь будет логика сбора статистики
        $this->view->render('warehouses/statistics/index', [
            'title' => 'Статистика',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }

    public function inventory() {
        $inventoryModel = new \App\Models\Inventory();
        $warehouseModel = new \App\Models\Warehouse();
        $categoryModel = new \App\Models\ItemCategory();

        $filters = [
            'warehouse_id' => isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : null,
            'category_id' => isset($_GET['category_id']) ? (int)$_GET['category_id'] : null,
            'search' => isset($_GET['search']) ? trim($_GET['search']) : null,
            'has_volume' => isset($_GET['has_volume']) && $_GET['has_volume'] !== '' ? (int)$_GET['has_volume'] : null
        ];

        $items = $inventoryModel->getAllInventory($filters);
        $warehouses = $warehouseModel->getAllWarehouses();
        $categories = $categoryModel->getAllCategories();

        $this->view->render('warehouses/inventory/index', [
            'items' => $items,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'filters' => $filters,
            'title' => 'Остатки',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['userPermissions'] : []
        ]);
    }
    
    /**
     * Генерация отчета по текущим остаткам
     * 
     * @return array
     */
    private function generateInventoryReport() {
        $inventoryModel = new \App\Models\Inventory();
        $warehouseId = isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : null;
        
        return $inventoryModel->getInventoryReport($warehouseId);
    }
    
    /**
     * Генерация отчета по движению товаров
     * 
     * @return array
     */
    private function generateMovementsReport() {
        $operationModel = new \App\Models\Operation();
        
        $filters = [
            'warehouse_id' => isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : null,
            'item_id' => isset($_GET['item_id']) ? (int)$_GET['item_id'] : null,
            'date_from' => isset($_GET['date_from']) ? $_GET['date_from'] : null,
            'date_to' => isset($_GET['date_to']) ? $_GET['date_to'] : null
        ];
        
        return $operationModel->getAllOperations($filters);
    }
    
    /**
     * Генерация отчета по приёмкам
     * 
     * @return array
     */
    private function generateReceiptsReport() {
        $operationModel = new \App\Models\Operation();
        
        $filters = [
            'warehouse_id' => isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : null,
            'item_id' => isset($_GET['item_id']) ? (int)$_GET['item_id'] : null,
            'date_from' => isset($_GET['date_from']) ? $_GET['date_from'] : null,
            'date_to' => isset($_GET['date_to']) ? $_GET['date_to'] : null,
            'operation_type' => \App\Models\Operation::TYPE_RECEPTION
        ];
        
        return $operationModel->getAllOperations($filters);
    }
    
    /**
     * Генерация отчета по выдачам
     * 
     * @return array
     */
    private function generateIssuesReport() {
        $operationModel = new \App\Models\Operation();
        
        $filters = [
            'warehouse_id' => isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : null,
            'item_id' => isset($_GET['item_id']) ? (int)$_GET['item_id'] : null,
            'date_from' => isset($_GET['date_from']) ? $_GET['date_from'] : null,
            'date_to' => isset($_GET['date_to']) ? $_GET['date_to'] : null,
            'operation_type' => \App\Models\Operation::TYPE_ISSUE
        ];
        
        return $operationModel->getAllOperations($filters);
    }
    
    /**
     * Генерация отчета по списаниям
     * 
     * @return array
     */
    private function generateWriteoffsReport() {
        $operationModel = new \App\Models\Operation();
        
        $filters = [
            'warehouse_id' => isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : null,
            'item_id' => isset($_GET['item_id']) ? (int)$_GET['item_id'] : null,
            'date_from' => isset($_GET['date_from']) ? $_GET['date_from'] : null,
            'date_to' => isset($_GET['date_to']) ? $_GET['date_to'] : null,
            'operation_type' => \App\Models\Operation::TYPE_WRITEOFF
        ];
        
        return $operationModel->getAllOperations($filters);
    }
    
    /**
     * Объединение данных поставщиков
     */
    public function mergeSuppliers() {
        // Проверяем наличие прав на управление
        $this->middleware('permission', ['maslosklad.manage']);
        
        $supplierModel = new \App\Models\Supplier();
        
        // Шаг 1: Объединяем данные из старой таблицы
        $migrationResult = $supplierModel->mergeSuppliers();
        
        // Шаг 2: Объединяем дублирующихся поставщиков
        $mergeResult = $supplierModel->mergeDuplicates();
        
        // Формируем сообщение с результатами
        $message = "Результаты объединения поставщиков:<br>";
        $message .= "Миграция из старой таблицы: {$migrationResult['migrated']} добавлено, {$migrationResult['updated']} обновлено, {$migrationResult['skipped']} пропущено.<br>";
        $message .= "Объединение дубликатов: {$mergeResult['merged']} объединено, {$mergeResult['deleted']} удалено, {$mergeResult['skipped']} пропущено.";
        
        if ($migrationResult['errors'] > 0 || $mergeResult['errors'] > 0) {
            $_SESSION['error'] = $message . "<br>Произошли ошибки во время объединения. Проверьте логи.";
        } else {
            $_SESSION['success'] = $message;
        }
        
        $this->redirect('/warehouses/suppliers');
    }
    
    /**
     * Просмотр поставщика
     */
    public function viewSupplier($id) {
        $supplierModel = new \App\Models\Supplier();
        $supplier = $supplierModel->getSupplierById($id);
        if (!$supplier) {
            $this->view->renderError('Поставщик не найден', 'Поставщик с таким ID не найден.');
            return;
        }
        $this->view->render('warehouses/suppliers/view', [
            'supplier' => $supplier,
            'title' => 'Просмотр поставщика'
        ]);
    }

    /**
     * Редактирование поставщика
     */
    public function editSupplier($id) {
        $supplierModel = new \App\Models\Supplier();
        $supplier = $supplierModel->getSupplierById($id);
        if (!$supplier) {
            $this->view->renderError('Поставщик не найден', 'Поставщик с таким ID не найден.');
            return;
        }
        $this->view->render('warehouses/suppliers/edit', [
            'supplier' => $supplier,
            'title' => 'Редактировать поставщика'
        ]);
    }

    /**
     * Страница создания нового поставщика
     */
    public function createSupplier() {
        $this->view->render('warehouses/suppliers/create', [
            'title' => 'Добавить поставщика'
        ]);
    }

    /**
     * Раздел Имущество
     */
    public function items() {
        $itemModel = new \App\Models\Item();
        $categoryModel = new \App\Models\ItemCategory();
        $warehouseTypeId = isset($_GET['warehouse_type_id']) ? (int)$_GET['warehouse_type_id'] : null;
        $categories = [];
        $items = [];
        if ($warehouseTypeId) {
            $categories = $categoryModel->getCategoriesByWarehouseType($warehouseTypeId);
            $categoryIds = array_column($categories, 'id');
            if (!empty($categoryIds)) {
                // Получаем все товары по этим категориям
                foreach ($categoryIds as $catId) {
                    $catItems = $itemModel->getAllItems(['category_id' => $catId]);
                    $items = array_merge($items, $catItems);
                }
            }
        } else {
            $items = $itemModel->getAllItems();
            $categories = $categoryModel->getAllCategories();
        }
        $warehouseTypeModel = new \App\Models\WarehouseType();
        $warehouseTypes = $warehouseTypeModel->getAllTypes();
        $this->view->render('warehouses/items/index', [
            'items' => $items,
            'categories' => $categories,
            'warehouseTypes' => $warehouseTypes,
            'selectedWarehouseTypeId' => $warehouseTypeId,
            'title' => 'Имущество',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }

    public function createItem() {
        $categoryModel = new \App\Models\ItemCategory();
        $categories = $categoryModel->getAllCategories();
        $this->view->render('warehouses/items/create', [
            'categories' => $categories,
            'title' => 'Добавить имущество',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }

    public function storeItem() {
        $itemModel = new \App\Models\Item();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $itemModel->createItem($data);
            $_SESSION['success'] = 'Имущество успешно добавлено';
            $this->redirect('/warehouses/items');
        }
    }

    public function editItem($id) {
        $itemModel = new \App\Models\Item();
        $categoryModel = new \App\Models\ItemCategory();
        $item = $itemModel->getItemById($id);
        $categories = $categoryModel->getAllCategories();
        $this->view->render('warehouses/items/edit', [
            'item' => $item,
            'categories' => $categories,
            'title' => 'Редактировать имущество',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }

    public function updateItem($id) {
        $itemModel = new \App\Models\Item();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $itemModel->updateItem($id, $data);
            $_SESSION['success'] = 'Имущество успешно обновлено';
            $this->redirect('/warehouses/items');
        }
    }

    public function deleteItem($id) {
        $itemModel = new \App\Models\Item();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemModel->deleteItem($id);
            $_SESSION['success'] = 'Имущество удалено';
            $this->redirect('/warehouses/items');
        }
    }

    public function searchItems() {
        $itemModel = new \App\Models\Item();
        $categoryModel = new \App\Models\ItemCategory();
        $warehouseTypeId = isset($_GET['warehouse_type_id']) ? (int)$_GET['warehouse_type_id'] : null;
        $query = $_GET['q'] ?? '';
        $items = [];
        if ($warehouseTypeId) {
            $categories = $categoryModel->getCategoriesByWarehouseType($warehouseTypeId);
            $categoryIds = array_column($categories, 'id');
            if (!empty($categoryIds)) {
                if (trim($query) === '') {
                    // Пустой запрос — вернуть все товары по выбранному типу склада
                    foreach ($categoryIds as $catId) {
                        $catItems = $itemModel->getAllItems(['category_id' => $catId]);
                        $items = array_merge($items, $catItems);
                    }
                } else {
                    foreach ($categoryIds as $catId) {
                        $catItems = $itemModel->searchItemsByCategory($query, $catId);
                        $items = array_merge($items, $catItems);
                    }
                }
            }
        } else {
            $items = $itemModel->searchItems($query);
        }
        header('Content-Type: application/json');
        echo json_encode($items);
        exit;
    }

    /**
     * Раздел Категории имущества
     */
    public function itemCategories() {
        $categoryModel = new \App\Models\ItemCategory();
        $warehouseTypeModel = new \App\Models\WarehouseType();
        $warehouseTypes = $warehouseTypeModel->getAllTypes();
        $filters = [];
        if (!empty($_GET['warehouse_type_id'])) {
            $filters['warehouse_type_id'] = (int)$_GET['warehouse_type_id'];
        }
        if (!empty($_GET['q'])) {
            $filters['q'] = trim($_GET['q']);
        }
        // Получаем категории с JOIN на warehouse_types
        $categories = $categoryModel->getAllCategoriesWithType($filters);
        // Фильтрация по поиску
        if (!empty($filters['q'])) {
            $categories = array_filter($categories, function($cat) use ($filters) {
                return mb_stripos($cat['name'], $filters['q']) !== false;
            });
        }
        $this->view->render('warehouses/items/categories/index', [
            'categories' => $categories,
            'warehouseTypes' => $warehouseTypes,
            'title' => 'Категории имущества',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }

    public function createItemCategory() {
        $warehouseTypeModel = new \App\Models\WarehouseType();
        $warehouseTypes = $warehouseTypeModel->getAllTypes();
        $this->view->render('warehouses/items/categories/create', [
            'warehouseTypes' => $warehouseTypes,
            'title' => 'Добавить категорию имущества',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }

    public function storeItemCategory() {
        $categoryModel = new \App\Models\ItemCategory();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $categoryModel->createCategory($data);
            $_SESSION['success'] = 'Категория успешно добавлена';
            $this->redirect('/warehouses/items/categories');
        }
    }

    public function editItemCategory($id) {
        $categoryModel = new \App\Models\ItemCategory();
        $warehouseTypeModel = new \App\Models\WarehouseType();
        $category = $categoryModel->getCategoryById($id);
        $warehouseTypes = $warehouseTypeModel->getAllTypes();
        $this->view->render('warehouses/items/categories/edit', [
            'category' => $category,
            'warehouseTypes' => $warehouseTypes,
            'title' => 'Редактировать категорию имущества',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }

    public function updateItemCategory($id) {
        $categoryModel = new \App\Models\ItemCategory();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $categoryModel->updateCategory($id, $data);
            $_SESSION['success'] = 'Категория успешно обновлена';
            $this->redirect('/warehouses/items/categories');
        }
    }

    public function deleteItemCategory($id) {
        $itemModel = new \App\Models\Item();
        $categoryModel = new \App\Models\ItemCategory();
        $items = $itemModel->getItemsByCategory($id);
        if (!empty($items)) {
            // Формируем уведомление с частью товаров
            $sample = array_slice($items, 0, 5);
            $names = array_map(function($item) { return $item['name']; }, $sample);
            $msg = 'Нельзя удалить категорию, к ней привязаны товары: <ul><li>' . implode('</li><li>', $names) . '</li></ul>';
            if (count($items) > 5) $msg .= '<div class="text-muted">и ещё ' . (count($items) - 5) . '...</div>';
            $_SESSION['error'] = $msg;
            $this->redirect('/warehouses/items/categories');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryModel->deleteCategory($id);
            $_SESSION['success'] = 'Категория удалена';
            $this->redirect('/warehouses/items/categories');
        }
    }

    public function searchItemCategories() {
        $categoryModel = new \App\Models\ItemCategory();
        $query = $_GET['q'] ?? '';
        $categories = $categoryModel->searchCategories($query);
        header('Content-Type: application/json');
        echo json_encode($categories);
        exit;
    }

    public function itemHistory($id) {
        $itemModel = new \App\Models\Item();
        $inventoryModel = new \App\Models\Inventory();
        $operationModel = new \App\Models\Operation();
        $warehouseModel = new \App\Models\Warehouse();

        $item = $itemModel->getItemById($id);
        if (!$item) {
            $this->view->renderError('Имущество не найдено', 'Товар с таким ID не найден.');
            return;
        }
        // Остатки по складам
        $inventories = $inventoryModel->getItemInventory($id);
        // История операций
        $operations = $operationModel->getAllOperations(['item_id' => $id]);
        // Все склады (для отображения названий)
        $warehouses = $warehouseModel->getAllWarehouses();
        $warehousesById = [];
        foreach ($warehouses as $w) {
            $warehousesById[$w['id']] = $w;
        }
        $this->view->render('warehouses/items/history', [
            'item' => $item,
            'inventories' => $inventories,
            'operations' => $operations,
            'warehousesById' => $warehousesById,
            'title' => 'История имущества',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
        ]);
    }

    public function searchInventory() {
        $inventoryModel = new \App\Models\Inventory();
        $filters = [
            'warehouse_id' => isset($_GET['warehouse_id']) ? (int)$_GET['warehouse_id'] : null,
            'category_id' => isset($_GET['category_id']) ? (int)$_GET['category_id'] : null,
            'search' => isset($_GET['search']) ? trim($_GET['search']) : null,
            'has_volume' => isset($_GET['has_volume']) && $_GET['has_volume'] !== '' ? (int)$_GET['has_volume'] : null
        ];
        $items = $inventoryModel->getAllInventory($filters);
        ob_start();
        if (empty($items)) {
            echo '<tr><td colspan="5" class="text-center py-4"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><p class="text-muted">Нет данных по остаткам</p></td></tr>';
        } else {
            foreach ($items as $item) {
                $isVolume = !empty($item['has_volume']);
                $value = $isVolume ? $item['volume'] : $item['quantity'];
                $value = ($value === null) ? '' : $value;
                echo '<tr>';
                echo '<td><a href="/warehouses/items/history/' . ($item['item_id'] ?? $item['id']) . '">' . htmlspecialchars($item['item_name'] ?? '') . '</a></td>';
                echo '<td>' . htmlspecialchars($item['last_update'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($item['category_name'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($item['warehouse_name'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($value) . ' ' . htmlspecialchars($item['unit'] ?? '') . '</td>';
                echo '</tr>';
            }
        }
        $html = ob_get_clean();
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }
} 