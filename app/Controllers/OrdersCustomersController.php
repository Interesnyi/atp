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
        // Обработка загрузки файла карточки организации
        if (isset($_FILES['org_card_file']) && $_FILES['org_card_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/org_cards/';
            $ext = pathinfo($_FILES['org_card_file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '-' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $_FILES['org_card_file']['name']);
            $uploadFile = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['org_card_file']['tmp_name'], $uploadFile)) {
                $data['org_card_file'] = '/uploads/org_cards/' . $filename;
            }
        }
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
        // Обработка загрузки файла карточки организации
        if (isset($_FILES['org_card_file']) && $_FILES['org_card_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/org_cards/';
            $ext = pathinfo($_FILES['org_card_file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '-' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $_FILES['org_card_file']['name']);
            $uploadFile = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['org_card_file']['tmp_name'], $uploadFile)) {
                $data['org_card_file'] = '/uploads/org_cards/' . $filename;
            }
        } else {
            // Если файл не загружен, сохраняем старое значение
            $customer = $model->getCustomerById($id);
            if (!empty($customer['org_card_file'])) {
                $data['org_card_file'] = $customer['org_card_file'];
            }
        }
        $model->updateCustomer($id, $data);
        $this->redirect('/orders/customers');
    }
    public function delete($id) {
        $model = new Customer();
        $model->deleteCustomer($id);
        $this->redirect('/orders/customers');
    }
} 