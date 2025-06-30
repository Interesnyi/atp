<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contract;
use App\Models\Customer;

class ContractsController extends Controller {
    public function index() {
        $contracts = (new Contract())->getAll();
        $this->view->render('contracts/index', [
            'contracts' => $contracts,
            'title' => 'Договоры'
        ]);
    }

    public function create() {
        $customers = (new Customer())->getAllCustomers();
        $this->view->render('contracts/create', [
            'customers' => $customers,
            'title' => 'Новый договор'
        ]);
    }

    public function store() {
        $model = new Contract();
        $data = $_POST;
        // Загрузка файла договора
        if (isset($_FILES['contract_file']) && $_FILES['contract_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/contracts/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $filename = uniqid() . '-' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $_FILES['contract_file']['name']);
            $uploadFile = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['contract_file']['tmp_name'], $uploadFile)) {
                $data['contract_file'] = '/uploads/contracts/' . $filename;
            }
        }
        $model->create($data);
        $this->redirect('/contracts');
    }

    public function show($id) {
        $contract = (new Contract())->getById($id);
        $this->view->render('contracts/view', [
            'contract' => $contract,
            'title' => 'Просмотр договора'
        ]);
    }

    public function edit($id) {
        $model = new Contract();
        $contract = $model->getById($id);
        $customers = (new Customer())->getAllCustomers();
        $this->view->render('contracts/edit', [
            'contract' => $contract,
            'customers' => $customers,
            'title' => 'Редактировать договор'
        ]);
    }

    public function update($id) {
        $model = new Contract();
        $data = $_POST;
        // Загрузка нового файла договора
        if (isset($_FILES['contract_file']) && $_FILES['contract_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/contracts/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $filename = uniqid() . '-' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $_FILES['contract_file']['name']);
            $uploadFile = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['contract_file']['tmp_name'], $uploadFile)) {
                $data['contract_file'] = '/uploads/contracts/' . $filename;
            }
        } else {
            // Если файл не загружен, сохраняем старое значение
            $contract = $model->getById($id);
            if (!empty($contract['contract_file'])) {
                $data['contract_file'] = $contract['contract_file'];
            }
        }
        $model->update($id, $data);
        $this->redirect('/contracts');
    }

    public function delete($id) {
        $model = new Contract();
        $model->delete($id);
        $this->redirect('/contracts');
    }

    public function downloadDoc($id) {
        // Отключаем вывод ошибок, чтобы не портить docx
        ini_set('display_errors', 0);
        error_reporting(0);

        $contract = (new \App\Models\Contract())->getById($id);

        // Получаем все данные клиента по customer_id
        $customerModel = new \App\Models\Customer();
        $customer = $customerModel->getCustomerById($contract['customer_id']);

        $customerVars = [
            'customer_name' => $customer['company_name'] ?? '',
            'inn' => $customer['inn'] ?? '',
            'ogrn' => $customer['ogrn'] ?? '',
            'bank_name' => $customer['bank_name'] ?? '',
            'bik' => $customer['bik'] ?? '',
            'account_number' => $customer['account_number'] ?? '',
            'correspondent_account' => $customer['correspondent_account'] ?? '',
            'address' => $customer['address'] ?? '',
            'contact_person' => $customer['contact_person'] ?? '',
            'position' => $customer['position'] ?? '',
            'phone' => $customer['phone'] ?? '',
            'email' => $customer['email'] ?? '',
        ];

        $templatePath = __DIR__ . '/../../public/templates/contract_template.docx';
        if (!file_exists($templatePath)) {
            die('Шаблон Word не найден.');
        }
        require_once __DIR__ . '/../../vendor/autoload.php';
        $phpWord = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        $phpWord->setValue('contract_number', $contract['contract_number']);
        $phpWord->setValue('contract_date', date('d.m.Y', strtotime($contract['contract_date'])));
        $phpWord->setValue('contact_person_genitive', $contract['contact_person_genitive']);
        foreach ($customerVars as $key => $value) {
            $phpWord->setValue($key, $value);
        }
        $fileName = 'Договор №' . $contract['contract_number'] . '.docx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $phpWord->saveAs('php://output');
        exit;
    }
} 