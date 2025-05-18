<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Item;
use App\Models\Category;

class ApiController extends Controller {
    protected $itemModel;
    protected $categoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->itemModel = new Item();
        $this->categoryModel = new Category();
        
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
     * Проверка авторизации
     * 
     * @return bool
     */
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
} 