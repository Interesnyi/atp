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
            // Получаем все типы складов для отображения в меню
            $warehouseTypes = $this->warehouseTypeModel->getAllTypes();
            
            // Получаем все склады
            $warehouses = $this->warehouseModel->getAllWarehouses();
            
            // Группируем склады по типам
            $warehousesByType = [];
            foreach ($warehouses as $warehouse) {
                $typeId = $warehouse['type_id'];
                if (!isset($warehousesByType[$typeId])) {
                    $warehousesByType[$typeId] = [];
                }
                $warehousesByType[$typeId][] = $warehouse;
            }
            
            // Получаем права пользователя для отображения в шаблоне
            $userPermissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
            
            // Передаем данные в представление через шаблонизатор
            $this->view->render('warehouses/index', [
                'warehouseTypes' => $warehouseTypes,
                'warehousesByType' => $warehousesByType,
                'title' => 'Система управления складами',
                'userPermissions' => $userPermissions
            ]);
            
        } catch (\Exception $e) {
            // Если произошла ошибка, отображаем ее
            $this->view->renderError('Ошибка', $e->getMessage());
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
        
        // Получаем информацию о типе склада
        $warehouseType = $this->warehouseTypeModel->getTypeById($warehouse['type_id']);
        
        // В зависимости от типа склада загружаем соответствующий контроллер
        switch ($warehouseType['code']) {
            case 'material':
                $controller = new MaterialWarehouseController();
                $controller->index($id);
                break;
            case 'tool':
                $controller = new ToolWarehouseController();
                $controller->index($id);
                break;
            case 'oil':
                $controller = new OilWarehouseController();
                $controller->index($id);
                break;
            case 'autoparts':
                $controller = new AutoPartsController();
                $controller->index($id);
                break;
            default:
                // Если неизвестный тип склада, отображаем стандартную страницу
                $this->view->render('warehouses/warehouse', [
                    'warehouse' => $warehouse,
                    'warehouseType' => $warehouseType,
                    'title' => 'Склад: ' . $warehouse['name']
                ]);
                break;
        }
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
            'type_id' => (int)($_POST['type_id'] ?? 0),
            'location' => $_POST['location'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        
        // Валидация данных
        if (empty($data['name'])) {
            echo json_encode(['success' => false, 'message' => 'Название склада не может быть пустым']);
            return;
        }
        
        if ($data['type_id'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Необходимо выбрать тип склада']);
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
            'type_id' => (int)($_POST['type_id'] ?? 0),
            'location' => $_POST['location'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        
        // Валидация данных
        if (empty($data['name'])) {
            echo json_encode(['success' => false, 'message' => 'Название склада не может быть пустым']);
            return;
        }
        
        if ($data['type_id'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Необходимо выбрать тип склада']);
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
            $this->view->renderError('Ошибка', $e->getMessage());
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
            $this->view->renderError('Ошибка', $e->getMessage());
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
            $this->view->renderError('Ошибка', $e->getMessage());
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
            $this->view->renderError('Ошибка', $e->getMessage());
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
        // Проверяем наличие прав на просмотр статистики
        $this->middleware('permission', ['maslosklad.view']);
        
        // Получаем данные для статистики
        $operationModel = new \App\Models\Operation();
        $warehouseModel = new \App\Models\Warehouse();
        $itemModel = new \App\Models\Item();
        
        // Статистика по типам операций
        $operationsStats = [
            'receptions' => $operationModel->countOperationsByType(null, \App\Models\Operation::TYPE_RECEPTION),
            'issues' => $operationModel->countOperationsByType(null, \App\Models\Operation::TYPE_ISSUE),
            'writeoffs' => $operationModel->countOperationsByType(null, \App\Models\Operation::TYPE_WRITEOFF)
        ];
        
        // Количество складов
        $warehousesCount = count($warehouseModel->getAllWarehouses());
        
        // Количество товаров
        $itemsCount = count($itemModel->getAllItems());
        
        // Передаем данные в представление
        $this->view->render('warehouses/statistics/index', [
            'operationsStats' => $operationsStats,
            'warehousesCount' => $warehousesCount,
            'itemsCount' => $itemsCount,
            'title' => 'Статистика',
            'userPermissions' => isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []
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
} 