<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\WorkType;

class OrdersWorkTypesController extends Controller {
    public function index() {
        $workTypes = (new WorkType())->getAllWorkTypes();
        $this->view->render('warehouses/items/work_types', [
            'workTypes' => $workTypes,
            'title' => 'Справочник работ'
        ]);
    }
    public function create() {
        $this->view->render('warehouses/items/create_work_type', [
            'title' => 'Добавить работу'
        ]);
    }
    public function store() {
        $model = new WorkType();
        $data = $_POST;
        $model->createWorkType($data);
        $this->redirect('/orders/work_types');
    }
    public function show($id) {
        $model = new WorkType();
        $work = $model->getWorkTypeById($id);
        $this->view->render('warehouses/items/view_work_type', [
            'work' => $work,
            'title' => 'Просмотр работы'
        ]);
    }
    public function edit($id) {
        $model = new WorkType();
        $work = $model->getWorkTypeById($id);
        $this->view->render('warehouses/items/edit_work_type', [
            'work' => $work,
            'title' => 'Редактировать работу'
        ]);
    }
    public function update($id) {
        $model = new WorkType();
        $data = $_POST;
        $model->updateWorkType($id, $data);
        $this->redirect('/orders/work_types');
    }
    public function delete($id) {
        $model = new WorkType();
        $model->deleteWorkType($id);
        $this->redirect('/orders/work_types');
    }
} 