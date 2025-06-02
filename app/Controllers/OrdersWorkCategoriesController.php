<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\WorkCategory;
class OrdersWorkCategoriesController extends Controller {
    public function index() {
        $categories = (new WorkCategory())->getAllCategories();
        $this->view->render('warehouses/items/work_categories', [
            'categories' => $categories,
            'title' => 'Категории работ'
        ]);
    }
    public function create() {
        $this->view->render('warehouses/items/create_work_category', [
            'title' => 'Добавить категорию работ'
        ]);
    }
    public function store() {
        $model = new WorkCategory();
        $data = $_POST;
        $model->createCategory($data);
        $this->redirect('/orders/work_categories');
    }
    public function show($id) {
        $model = new WorkCategory();
        $category = $model->getCategoryById($id);
        $this->view->render('warehouses/items/view_work_category', [
            'category' => $category,
            'title' => 'Просмотр категории работ'
        ]);
    }
    public function edit($id) {
        $model = new WorkCategory();
        $category = $model->getCategoryById($id);
        $this->view->render('warehouses/items/edit_work_category', [
            'category' => $category,
            'title' => 'Редактировать категорию работ'
        ]);
    }
    public function update($id) {
        $model = new WorkCategory();
        $data = $_POST;
        $model->updateCategory($id, $data);
        $this->redirect('/orders/work_categories');
    }
    public function delete($id) {
        $model = new WorkCategory();
        $model->deleteCategory($id);
        $this->redirect('/orders/work_categories');
    }
} 