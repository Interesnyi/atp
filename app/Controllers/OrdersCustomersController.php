<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Customer;

class OrdersCustomersController extends Controller {
    public function index() {
        $customers = (new Customer())->getAllCustomers();
        $this->view->render('warehouses/customers/index', [
            'customers' => $customers,
            'title' => 'Клиенты'
        ]);
    }
    public function create() {
        $this->view->render('warehouses/customers/create', [
            'title' => 'Добавить клиента'
        ]);
    }
    public function store() {
        $model = new Customer();
        $data = $_POST;
        $model->createCustomer($data);
        $this->redirect('/orders/customers');
    }
    public function show($id) {
        $model = new Customer();
        $customer = $model->getCustomerById($id);
        $this->view->render('warehouses/customers/view', [
            'customer' => $customer,
            'title' => 'Просмотр клиента'
        ]);
    }
    public function edit($id) {
        $model = new Customer();
        $customer = $model->getCustomerById($id);
        $this->view->render('warehouses/customers/edit', [
            'customer' => $customer,
            'title' => 'Редактировать клиента'
        ]);
    }
    public function update($id) {
        $model = new Customer();
        $data = $_POST;
        $model->updateCustomer($id, $data);
        $this->redirect('/orders/customers');
    }
    public function delete($id) {
        $model = new Customer();
        $model->deleteCustomer($id);
        $this->redirect('/orders/customers');
    }
} 