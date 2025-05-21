<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Supplier;

class ApiController extends Controller {
    protected $itemModel;
    protected $categoryModel;
    protected $supplierModel;
    
    public function __construct() {
        parent::__construct();
        $this->itemModel = new Item();
        $this->categoryModel = new Category();
        $this->supplierModel = new Supplier();
        
        // Добавляем middleware для проверки авторизации
        $this->middleware('auth');
    }
    
    /**
     * Получение товаров по категории
     * 
     * @param int $categoryId ID категории
     * @return void
     */
    public function itemsByCategory($categoryId) {
        // Проверяем, что запрос от авторизованного пользователя
        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Не авторизован']);
            return;
        }
        
        // Получаем товары для указанной категории
        $items = $this->itemModel->getItemsByCategory($categoryId);
        
        // Форматируем данные для ответа
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'article' => $item['article'],
                'unit' => $item['unit'],
                'has_volume' => (bool)$item['has_volume']
            ];
        }
        
        // Отправляем ответ
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'items' => $formattedItems
        ]);
    }
    
    /**
     * Получение остатков товара на складах
     * 
     * @param int $itemId ID товара
     * @return void
     */
    public function itemInventory($itemId) {
        // Проверяем, что запрос от авторизованного пользователя
        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Не авторизован']);
            return;
        }
        
        // Получаем информацию о товаре
        $item = $this->itemModel->getItemById($itemId);
        if (!$item) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Товар не найден']);
            return;
        }
        
        // Получаем остатки товара на всех складах
        $inventory = new \App\Models\Inventory();
        $inventoryData = $inventory->getItemInventory($itemId);
        
        // Форматируем данные для ответа
        $formattedInventory = [];
        $totalQuantity = 0;
        $totalVolume = 0;
        
        foreach ($inventoryData as $record) {
            $formattedInventory[] = [
                'warehouse_id' => $record['warehouse_id'],
                'warehouse_name' => $record['warehouse_name'],
                'warehouse_type' => $record['warehouse_type_name'],
                'quantity' => $record['quantity'],
                'volume' => $record['volume'] ?? 0
            ];
            
            $totalQuantity += $record['quantity'];
            $totalVolume += $record['volume'] ?? 0;
        }
        
        // Отправляем ответ
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'item' => [
                'id' => $item['id'],
                'name' => $item['name'],
                'article' => $item['article'],
                'unit' => $item['unit'],
                'has_volume' => (bool)$item['has_volume']
            ],
            'inventory' => $formattedInventory,
            'total_quantity' => $totalQuantity,
            'total_volume' => $totalVolume
        ]);
    }
    
    /**
     * Получение данных поставщика по ID
     * 
     * @param int $id ID поставщика
     * @return void
     */
    public function getSupplier($id) {
        // Проверяем, что запрос от авторизованного пользователя
        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Не авторизован']);
            return;
        }
        
        // Получаем данные поставщика
        $supplier = $this->supplierModel->getSupplierById($id);
        
        if (!$supplier) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Поставщик не найден']);
            return;
        }
        
        // Отправляем ответ
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'supplier' => $supplier
        ]);
    }
    
    /**
     * Создание нового поставщика
     * 
     * @return void
     */
    public function createSupplier() {
        // Проверяем, что запрос от авторизованного пользователя
        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Не авторизован']);
            return;
        }
        
        // Проверяем, что запрос отправлен методом POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            return;
        }
        
        // Валидация данных
        if (empty($_POST['name'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Название поставщика не может быть пустым']);
            return;
        }
        
        // Подготовка данных
        $data = [
            'name' => $_POST['name'],
            'inn' => !empty($_POST['inn']) ? $_POST['inn'] : null,
            'contact_person' => !empty($_POST['contact_person']) ? $_POST['contact_person'] : null,
            'phone' => !empty($_POST['phone']) ? $_POST['phone'] : null,
            'email' => !empty($_POST['email']) ? $_POST['email'] : null,
            'address' => !empty($_POST['address']) ? $_POST['address'] : null,
            'description' => !empty($_POST['description']) ? $_POST['description'] : null
        ];
        
        try {
            $id = $this->supplierModel->createSupplier($data);
            if ($id) {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Поставщик успешно добавлен']);
                } else {
                    $_SESSION['success_message'] = 'Поставщик успешно добавлен';
                    header('Location: /warehouses/suppliers');
                    exit;
                }
            } else {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Не удалось добавить поставщика']);
                } else {
                    $_SESSION['error'] = 'Не удалось добавить поставщика';
                    header('Location: /warehouses/suppliers/create');
                    exit;
                }
            }
        } catch (\Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
            } else {
                $_SESSION['error'] = 'Ошибка: ' . $e->getMessage();
                header('Location: /warehouses/suppliers/create');
                exit;
            }
        }
    }
    
    /**
     * Обновление данных поставщика
     * 
     * @param int $id ID поставщика
     * @return void
     */
    public function updateSupplier($id) {
        // Проверяем, что запрос от авторизованного пользователя
        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Не авторизован']);
            return;
        }
        
        // Проверяем, что запрос отправлен методом POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            return;
        }
        
        // Проверяем существование поставщика
        $supplier = $this->supplierModel->getSupplierById($id);
        if (!$supplier) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Поставщик не найден']);
            return;
        }
        
        // Валидация данных
        if (empty($_POST['name'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Название поставщика не может быть пустым']);
            return;
        }
        
        // Подготовка данных
        $data = [
            'name' => $_POST['name'],
            'inn' => !empty($_POST['inn']) ? $_POST['inn'] : null,
            'contact_person' => !empty($_POST['contact_person']) ? $_POST['contact_person'] : null,
            'phone' => !empty($_POST['phone']) ? $_POST['phone'] : null,
            'email' => !empty($_POST['email']) ? $_POST['email'] : null,
            'address' => !empty($_POST['address']) ? $_POST['address'] : null,
            'description' => !empty($_POST['description']) ? $_POST['description'] : null
        ];
        
        try {
            // Обновляем данные поставщика
            $result = $this->supplierModel->updateSupplier($id, $data);
            
            if ($result) {
                $updatedSupplier = $this->supplierModel->getSupplierById($id);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Данные поставщика успешно обновлены',
                    'supplier' => $updatedSupplier
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Не удалось обновить данные поставщика']);
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Удаление поставщика
     * 
     * @param int $id ID поставщика
     * @return void
     */
    public function deleteSupplier($id) {
        // Проверяем, что запрос от авторизованного пользователя
        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Не авторизован']);
            return;
        }
        
        // Проверяем, что запрос отправлен методом POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
            return;
        }
        
        // Проверяем существование поставщика
        $supplier = $this->supplierModel->getSupplierById($id);
        if (!$supplier) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Поставщик не найден']);
            return;
        }
        
        try {
            // Удаляем поставщика (мягкое удаление)
            $result = $this->supplierModel->deleteSupplier($id);
            
            if ($result) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Поставщик успешно удален'
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Не удалось удалить поставщика']);
            }
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Поиск поставщиков по названию
     * 
     * @return void
     */
    public function searchSuppliers() {
        // Проверяем, что запрос от авторизованного пользователя
        if (!$this->isAuthenticated()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Не авторизован']);
            return;
        }
        
        $searchTerm = $_GET['term'] ?? '';
        
        // Получаем список поставщиков
        $suppliers = $this->supplierModel->searchSuppliers($searchTerm);
        
        // Отправляем ответ
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'suppliers' => $suppliers
        ]);
    }
    
    /**
     * Проверка авторизации
     * 
     * @return bool
     */
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) || 
               isset($_SESSION['id']) && !empty($_SESSION['id']);
    }
} 