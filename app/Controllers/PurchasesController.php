<?php
namespace App\Controllers;
use App\Core\Controller;
class PurchasesController extends Controller {
    public function index() {
        $purchaseModel = new \App\Models\Purchase();
        $purchases = $purchaseModel->getAllPurchases();
        $this->view('purchases/index', ['purchases' => $purchases]);
    }
    public function create() {
        // Форма создания закупки
        $suppliers = (new \App\Models\Supplier())->getAllSuppliers();
        $categories = (new \App\Models\ItemCategory())->getAllCategories();
        $this->view('purchases/create', [
            'suppliers' => $suppliers,
            'categories' => $categories
        ]);
    }
    public function store() {
        $comment = $_POST['comment'] ?? '';
        $purchaseModel = new \App\Models\Purchase();
        $purchaseId = $purchaseModel->createPurchase([
            'comment' => $comment,
            'status' => 'new',
            'user_id' => null // добавить user_id если есть авторизация
        ]);
        $_SESSION['success'] = 'Закупка успешно создана! Теперь добавьте позиции.';
        header('Location: /purchases/edit/' . $purchaseId);
        exit;
    }
    public function edit($id) {
        $purchaseModel = new \App\Models\Purchase();
        $itemModel = new \App\Models\PurchaseItem();
        $categoryModel = new \App\Models\ItemCategory();
        $itemListModel = new \App\Models\Item();
        $purchase = $purchaseModel->getPurchaseById($id);
        if (!$purchase) {
            $_SESSION['error'] = 'Закупка не найдена.';
            header('Location: /purchases');
            exit;
        }
        $positions = $itemModel->getItemsByPurchaseId($id);
        $categories = $categoryModel->getAllCategories();
        $allItems = $itemListModel->getAllItems();
        $this->view('purchases/edit', [
            'purchase' => $purchase,
            'positions' => $positions,
            'categories' => $categories,
            'allItems' => $allItems
        ]);
    }
    public function update($id) {
        // Сохранение изменений закупки
    }
    public function delete($id) {
        // Удаление закупки
    }
    public function addItem($purchaseId) {
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $itemId = (int)($_POST['item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        if (!$categoryId || !$itemId || $quantity <= 0) {
            $_SESSION['error'] = 'Заполните все поля корректно.';
            header('Location: /purchases/edit/' . $purchaseId);
            exit;
        }
        $itemModel = new \App\Models\PurchaseItem();
        $itemModel->createItem([
            'purchase_id' => $purchaseId,
            'category_id' => $categoryId,
            'item_id' => $itemId,
            'quantity' => $quantity
        ]);
        $_SESSION['success'] = 'Позиция добавлена.';
        header('Location: /purchases/edit/' . $purchaseId);
        exit;
    }
} 