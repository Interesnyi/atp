<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Examination;
use App\Models\Customer;
use App\Models\Car;
use App\Models\Contract;

class ExaminationsController extends Controller {
    public function index() {
        $exams = (new Examination())->getAll();
        $this->view->render('examinations/index', [
            'exams' => $exams,
            'title' => 'Экспертизы'
        ]);
    }

    public function create() {
        $customers = (new Customer())->getAllCustomers();
        $contracts = (new Contract())->getAll();
        $this->view->render('examinations/create', [
            'customers' => $customers,
            'contracts' => $contracts,
            'title' => 'Новая экспертиза'
        ]);
    }

    public function store() {
        $model = new Examination();
        $data = $_POST;
        $model->create($data);
        $this->redirect('/examinations');
    }

    public function show($id) {
        $exam = (new Examination())->getById($id);
        $this->view->render('examinations/view', [
            'exam' => $exam,
            'title' => 'Просмотр экспертизы'
        ]);
    }

    public function edit($id) {
        $model = new Examination();
        $exam = $model->getById($id);
        $customers = (new Customer())->getAllCustomers();
        $contracts = (new Contract())->getAll();
        $this->view->render('examinations/edit', [
            'exam' => $exam,
            'customers' => $customers,
            'contracts' => $contracts,
            'title' => 'Редактировать экспертизу'
        ]);
    }

    public function update($id) {
        $model = new Examination();
        $data = $_POST;
        $model->update($id, $data);
        $this->redirect('/examinations');
    }

    public function delete($id) {
        $model = new Examination();
        $model->delete($id);
        $this->redirect('/examinations');
    }

    public function downloadDoc($id) {
        ini_set('display_errors', 0);
        error_reporting(0);
        $exam = (new Examination())->getById($id);
        $templatePath = __DIR__ . '/../../public/templates/examination_template.docx';
        if (!file_exists($templatePath)) {
            die('Шаблон Word не найден.');
        }
        require_once __DIR__ . '/../../vendor/autoload.php';
        $phpWord = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        $phpWord->setValue('contract_number', $exam['contract_number'] ?? '');
        $phpWord->setValue('contract_date', isset($exam['contract_date']) ? date('d.m.Y', strtotime($exam['contract_date'])) : '');
        $fileName = 'Экспертиза №' . $exam['id'] . '.docx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $phpWord->saveAs('php://output');
        exit;
    }
} 