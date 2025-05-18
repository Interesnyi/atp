<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\WarehouseType;
use App\Models\Warehouse;

class MasloskladController extends Controller {
    protected $warehouseTypeModel;
    protected $warehouseModel;
    
    public function __construct() {
        parent::__construct();
        $this->warehouseTypeModel = new WarehouseType();
        $this->warehouseModel = new Warehouse();
        
        // Добавляем middleware для проверки авторизации
        $this->middleware('auth');
        
        // Добавляем middleware для проверки прав доступа к модулю масло-склада
        $this->middleware('permission', ['maslosklad.view']);
    }
    
    /**
     * Главная страница модуля масло-склада
     */
    public function index() {
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
        
        // Передаем данные в представление
        $this->view->render('maslosklad/index', [
            'warehouseTypes' => $warehouseTypes,
            'warehousesByType' => $warehousesByType,
            'title' => 'Масло-склад'
        ]);
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
            $this->redirect('/maslosklad');
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
                $this->view->render('maslosklad/warehouse', [
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
        $this->view->render('maslosklad/warehouses', [
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
} 