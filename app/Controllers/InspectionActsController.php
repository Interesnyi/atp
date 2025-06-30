<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\InspectionAct;
use App\Models\Customer;
use App\Models\Car;

class InspectionActsController extends Controller {
    public function index() {
        $acts = (new InspectionAct())->getAll();
        $this->view->render('inspection_acts/index', [
            'acts' => $acts,
            'title' => 'Акты осмотра'
        ]);
    }

    public function create() {
        $customers = (new Customer())->getAllCustomers();
        $contracts = (new \App\Models\Contract())->getAll();
        $this->view->render('inspection_acts/create', [
            'customers' => $customers,
            'contracts' => $contracts,
            'title' => 'Новый акт осмотра'
        ]);
    }

    public function store() {
        $model = new InspectionAct();
        $data = $_POST;
        $model->create($data);
        $this->redirect('/inspection-acts');
    }

    public function show($id) {
        $act = (new InspectionAct())->getById($id);
        $this->view->render('inspection_acts/view', [
            'act' => $act,
            'title' => 'Просмотр акта осмотра'
        ]);
    }

    public function edit($id) {
        $model = new InspectionAct();
        $act = $model->getById($id);
        $customers = (new Customer())->getAllCustomers();
        $contracts = (new \App\Models\Contract())->getAll();
        $this->view->render('inspection_acts/edit', [
            'act' => $act,
            'customers' => $customers,
            'contracts' => $contracts,
            'title' => 'Редактировать акт осмотра'
        ]);
    }

    public function update($id) {
        $model = new InspectionAct();
        $data = $_POST;
        $model->update($id, $data);
        $this->redirect('/inspection-acts');
    }

    public function delete($id) {
        $model = new InspectionAct();
        $model->delete($id);
        $this->redirect('/inspection-acts');
    }

    public function downloadDoc($id) {
        ini_set('display_errors', 0);
        error_reporting(0);
        $act = (new \App\Models\InspectionAct())->getById($id);
        $templatePath = __DIR__ . '/../../public/templates/inspection_act_template.docx';
        if (!file_exists($templatePath)) {
            die('Шаблон Word не найден.');
        }
        require_once __DIR__ . '/../../vendor/autoload.php';
        $phpWord = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        $phpWord->setValue('date', date('d.m.Y', strtotime($act['date'])));
        $phpWord->setValue('customer', $act['company_name'] ?: $act['contact_person']);
        $phpWord->setValue('car', $act['brand'] . ' ' . $act['model'] . ' (' . $act['year'] . ', ' . $act['license_plate'] . ')');
        $phpWord->setValue('description', $act['description']);
        $phpWord->setValue('damages', $act['damages']);
        $phpWord->setValue('conclusion', $act['conclusion']);
        // Подстановка данных договора, если есть
        $phpWord->setValue('contract_number', $act['contract_number'] ?? '');
        $phpWord->setValue('contract_date', isset($act['contract_date']) ? date('d.m.Y', strtotime($act['contract_date'])) : '');
        $fileName = 'Акт осмотра №' . $act['id'] . '.docx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $phpWord->saveAs('php://output');
        exit;
    }
} 