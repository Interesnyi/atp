<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Car;
use App\Models\WorkType;
use App\Models\CustomerMaterial;
use App\Models\User;
use App\Models\OrderWork;

class OrdersController extends Controller {
    public function index() {
        $orders = (new Order())->getAllOrders();
        $this->view->render('orders/index', [
            'orders' => $orders,
            'title' => 'Заказ-наряды'
        ]);
    }

    public function create() {
        $customers = (new Customer())->getAllCustomers();
        $cars = [];
        $workTypes = (new WorkType())->getAllWorkTypes();
        $customerMaterials = (new CustomerMaterial())->getAllCustomerMaterials();
        $users = (new User())->getAllUsers();
        $this->view->render('orders/create', [
            'customers' => $customers,
            'cars' => $cars,
            'workTypes' => $workTypes,
            'customerMaterials' => $customerMaterials,
            'users' => $users,
            'title' => 'Создать заказ-наряд'
        ]);
    }

    public function store() {
        $orderModel = new Order();
        $data = $_POST;
        // Генерация номера заказ-наряда, если не передан
        if (empty($data['order_number'])) {
            $date = date('Ymd');
            $data['order_number'] = 'ORD-' . $date;
        }
        $orderId = $orderModel->createOrder($data);
        // Сохраняем работы
        if (!empty($data['works']) && is_array($data['works'])) {
            $workTypeModel = new WorkType();
            $orderWorkModel = new OrderWork();
            foreach ($data['works'] as $work) {
                if (empty($work['work_type_id'])) continue;
                $workType = $workTypeModel->getWorkTypeById($work['work_type_id']);
                $orderWorkModel->createWork([
                    'order_id' => $orderId,
                    'work_type_id' => $work['work_type_id'],
                    'name' => $workType['name'] ?? '',
                    'code' => $workType['code'] ?? '',
                    'quantity' => $work['quantity'] ?? 1,
                    'price' => $work['price'] ?? 0,
                    'total' => $work['total'] ?? 0,
                    'executor' => $work['executor'] ?? null
                ]);
            }
        }
        header('Location: /orders/view/' . $orderId);
        exit;
    }

    public function show($id) {
        $orderModel = new Order();
        $order = $orderModel->getOrderById($id);
        $works = $orderModel->getWorks($id);
        $materials = $orderModel->getMaterials($id);
        $customerMaterials = $orderModel->getCustomerMaterials($id);
        $files = $orderModel->getFiles($id);
        $this->view->render('orders/view', [
            'order' => $order,
            'works' => $works,
            'materials' => $materials,
            'customerMaterials' => $customerMaterials,
            'files' => $files,
            'title' => 'Просмотр заказ-наряда'
        ]);
    }

    public function edit($id) {
        $orderModel = new Order();
        $order = $orderModel->getOrderById($id);
        $customers = (new Customer())->getAllCustomers();
        $cars = (new Car())->getCarsByCustomer($order['customer_id']);
        $workTypes = (new WorkType())->getAllWorkTypes();
        $customerMaterials = (new CustomerMaterial())->getAllCustomerMaterials();
        $works = $orderModel->getWorks($id);
        $materials = $orderModel->getMaterials($id);
        $customerMats = $orderModel->getCustomerMaterials($id);
        $files = $orderModel->getFiles($id);
        $users = (new User())->getAllUsers();
        $this->view->render('orders/edit', [
            'order' => $order,
            'customers' => $customers,
            'cars' => $cars,
            'workTypes' => $workTypes,
            'customerMaterials' => $customerMaterials,
            'works' => $works,
            'materials' => $materials,
            'customerMats' => $customerMats,
            'files' => $files,
            'users' => $users,
            'title' => 'Редактировать заказ-наряд'
        ]);
    }

    public function update($id) {
        $orderModel = new Order();
        $data = $_POST;
        $orderModel->updateOrder($id, $data);
        // Обновляем работы
        $orderWorkModel = new OrderWork();
        $orderWorkModel->deleteWorksByOrder($id);
        if (!empty($data['works']) && is_array($data['works'])) {
            $workTypeModel = new WorkType();
            foreach ($data['works'] as $work) {
                if (empty($work['work_type_id'])) continue;
                $workType = $workTypeModel->getWorkTypeById($work['work_type_id']);
                $orderWorkModel->createWork([
                    'order_id' => $id,
                    'work_type_id' => $work['work_type_id'],
                    'name' => $workType['name'] ?? '',
                    'code' => $workType['code'] ?? '',
                    'quantity' => $work['quantity'] ?? 1,
                    'price' => $work['price'] ?? 0,
                    'total' => $work['total'] ?? 0,
                    'executor' => $work['executor'] ?? null
                ]);
            }
        }
        header('Location: /orders/view/' . $id);
        exit;
    }

    public function delete($id) {
        $orderModel = new Order();
        $orderModel->deleteOrder($id);
        header('Location: /orders');
        exit;
    }
} 