<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Car;
use App\Models\Customer;

class OrdersCarsController extends Controller {
    public function index() {
        $cars = (new Car())->getAllCarsWithCustomer();
        $this->view->render('warehouses/items/cars', [
            'cars' => $cars,
            'title' => 'Автомобили'
        ]);
    }
    public function create() {
        $customers = (new Customer())->getAllCustomers();
        $this->view->render('warehouses/items/create_car', [
            'customers' => $customers,
            'title' => 'Добавить автомобиль'
        ]);
    }
    public function store() {
        $model = new Car();
        $data = $_POST;
        $model->createCar($data);
        $this->redirect('/orders/cars');
    }
    public function show($id) {
        $model = new Car();
        $car = $model->getCarByIdWithCustomer($id);
        $this->view->render('warehouses/items/view_car', [
            'car' => $car,
            'title' => 'Просмотр автомобиля'
        ]);
    }
    public function edit($id) {
        $model = new Car();
        $customers = (new Customer())->getAllCustomers();
        $car = $model->getCarById($id);
        $this->view->render('warehouses/items/edit_car', [
            'car' => $car,
            'customers' => $customers,
            'title' => 'Редактировать автомобиль'
        ]);
    }
    public function update($id) {
        $model = new Car();
        $data = $_POST;
        $model->updateCar($id, $data);
        $this->redirect('/orders/cars');
    }
    public function delete($id) {
        $model = new Car();
        $model->deleteCar($id);
        $this->redirect('/orders/cars');
    }
    public function getCarsByCustomer($customerId) {
        $cars = (new Car())->getCarsByCustomer($customerId);
        header('Content-Type: application/json');
        echo json_encode($cars);
        exit;
    }
} 