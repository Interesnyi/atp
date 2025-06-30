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
        $contracts = (new \App\Models\Contract())->getAll();
        $parts = (new \App\Models\Part())->getAllParts();
        $this->view->render('orders/create', [
            'customers' => $customers,
            'cars' => $cars,
            'workTypes' => $workTypes,
            'customerMaterials' => $customerMaterials,
            'users' => $users,
            'contracts' => $contracts,
            'parts' => $parts,
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
        // Сохраняем запчасти
        if (!empty($data['order_parts']) && is_array($data['order_parts'])) {
            $orderPartModel = new \App\Models\OrderPart();
            foreach ($data['order_parts'] as $part) {
                if (empty($part['part_id'])) continue;
                $orderPartModel->createPart([
                    'order_id' => $orderId,
                    'part_id' => $part['part_id'],
                    'quantity' => $part['quantity'] ?? 1,
                    'price' => $part['price'] ?? 0,
                    'total' => $part['total'] ?? 0
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
        $orderParts = (new \App\Models\OrderPart())->getParts($id);
        $this->view->render('orders/view', [
            'order' => $order,
            'works' => $works,
            'materials' => $materials,
            'customerMaterials' => $customerMaterials,
            'files' => $files,
            'orderParts' => $orderParts,
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
        $contracts = (new \App\Models\Contract())->getAll();
        $parts = (new \App\Models\Part())->getAllParts();
        $orderParts = (new \App\Models\OrderPart())->getParts($id);
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
            'contracts' => $contracts,
            'parts' => $parts,
            'orderParts' => $orderParts,
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
        // Обновляем запчасти
        $orderPartModel = new \App\Models\OrderPart();
        $orderPartModel->deletePartsByOrder($id);
        if (!empty($data['order_parts']) && is_array($data['order_parts'])) {
            foreach ($data['order_parts'] as $part) {
                if (empty($part['part_id'])) continue;
                $orderPartModel->createPart([
                    'order_id' => $id,
                    'part_id' => $part['part_id'],
                    'quantity' => $part['quantity'] ?? 1,
                    'price' => $part['price'] ?? 0,
                    'total' => $part['total'] ?? 0
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

    public function downloadDoc($id) {
        // Временно включаем вывод ошибок для отладки
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        $orderModel = new Order();
        $order = $orderModel->getOrderById($id);
        $works = $orderModel->getWorks($id);
        $materials = $orderModel->getMaterials($id);
        $customerMaterials = $orderModel->getCustomerMaterials($id);
        $customer = (new Customer())->getCustomerById($order['customer_id']);
        $car = (new Car())->getCarById($order['car_id']);
        
        // Исправленный путь к шаблону
        $templatePath = __DIR__ . '/../../public/templates/order_template.docx';
        if (!file_exists($templatePath)) {
            file_put_contents(__DIR__.'/../../logs/debug.log', "[downloadDoc] Шаблон не найден: $templatePath\n", FILE_APPEND);
            die('Шаблон не найден: ' . $templatePath);
        }
        $phpWord = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // Формируем таблицу работ через cloneRow
        $phpWord->cloneRow('work_name', count($works));
        $totalWorks = 0;
        foreach ($works as $i => $w) {
            $n = $i + 1;
            $phpWord->setValue("work_name#$n", $w['name'] ?? '');
            $phpWord->setValue("qty#$n", $w['quantity'] ?? '');
            $phpWord->setValue("price#$n", $w['price'] ?? '');
            $phpWord->setValue("sum#$n", $w['total'] ?? '');
            $totalWorks += (float)($w['total'] ?? 0);
        }

        // Тестовая подстановка для отладки
        $phpWord->setValue('works_total', number_format($totalWorks, 2, '.', ' '));
        $phpWord->setValue('contract_number', $order['contract_number'] ?? '');
        $phpWord->setValue('contract_date', !empty($order['contract_date']) ? date('d.m.Y', strtotime($order['contract_date'])) : '');
        $phpWord->setValue('order_number', $order['order_number'] ?? '');
        $phpWord->setValue('order_date', !empty($order['date_created']) ? date('d.m.Y', strtotime($order['date_created'])) : '');
        $phpWord->setValue('company_name', $customer['company_name'] ?? $customer['contact_person'] ?? '');
        $phpWord->setValue('inn', $customer['inn'] ?? '');
        $phpWord->setValue('address', $customer['address'] ?? '');
        $phpWord->setValue('phone', $customer['phone'] ?? '');
        $phpWord->setValue('email', $customer['email'] ?? '');
        $phpWord->setValue('brand', $car['brand'] ?? '');
        $phpWord->setValue('model', $car['model'] ?? '');
        $phpWord->setValue('year', $car['year'] ?? '');
        $phpWord->setValue('license_plate', $car['license_plate'] ?? '');
        $phpWord->setValue('body_number', $car['body_number'] ?? '');
        $phpWord->setValue('vin', $car['vin'] ?? '');
        // ... остальные переменные по необходимости ...

        $filename = 'order_' . ($order['order_number'] ?? $id) . '.docx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $phpWord->saveAs('php://output');
        exit;
    }
} 