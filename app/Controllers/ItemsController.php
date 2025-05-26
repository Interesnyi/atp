<?php

use App\Models\Item;

class ItemsController {
    protected $itemModel;

    public function __construct() {
        $this->itemModel = new Item();
    }

    public function index() {
        $items = $this->itemModel->getAllItems();
        include __DIR__ . '/../Views/warehouses/items/index.php';
    }

    public function create() {
        include __DIR__ . '/../Views/warehouses/items/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $this->itemModel->createItem($data);
            header('Location: /warehouses/items');
            exit;
        }
    }

    public function edit($id) {
        $item = $this->itemModel->getItemById($id);
        include __DIR__ . '/../Views/warehouses/items/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $this->itemModel->updateItem($id, $data);
            header('Location: /warehouses/items');
            exit;
        }
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->itemModel->deleteItem($id);
            header('Location: /warehouses/items');
            exit;
        }
    }
} 