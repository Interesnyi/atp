<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Invoice;
use App\Models\LegalEntity;
use App\Models\InvoiceItem;
use App\Models\InvoiceFile;
use App\Models\Operation;

class InvoicesController extends Controller {
    public function index() {
        $invoiceModel = new Invoice();
        $filters = $_GET;
        $invoices = $invoiceModel->getAll($filters);
        $this->view->render('invoices/index', [
            'invoices' => $invoices,
            'filters' => $filters
        ]);
    }

    public function show($id) {
        $invoiceModel = new Invoice();
        $fileModel = new InvoiceFile();
        $operationModel = new Operation();
        $invoice = $invoiceModel->getById($id);
        $files = $fileModel->getByInvoice($id);
        $items = [];
        if (!empty($invoice['number'])) {
            // document_number = номер счёта
            $items = $operationModel->getAllOperations(['document_number' => $invoice['number']]);
        }
        $this->view->render('invoices/show', [
            'invoice' => $invoice,
            'items' => $items,
            'files' => $files
        ]);
    }

    public function create() {
        $legalModel = new LegalEntity();
        $legalEntities = $legalModel->getAll();
        $this->view->render('invoices/create', [
            'legalEntities' => $legalEntities
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoiceModel = new Invoice();
            $data = $_POST;
            foreach (['date_issued', 'date_shipped', 'date_paid'] as $dateField) {
                if (empty($data[$dateField])) {
                    $data[$dateField] = null;
                }
            }
            $id = $invoiceModel->create($data);
            header('Location: /invoices/show/' . $id);
            exit;
        }
        header('Location: /invoices');
        exit;
    }

    public function edit($id) {
        $invoiceModel = new Invoice();
        $legalModel = new LegalEntity();
        $invoice = $invoiceModel->getById($id);
        $legalEntities = $legalModel->getAll();
        $this->view->render('invoices/edit', [
            'invoice' => $invoice,
            'legalEntities' => $legalEntities
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoiceModel = new Invoice();
            $data = $_POST;
            foreach (['date_issued', 'date_shipped', 'date_paid'] as $dateField) {
                if (empty($data[$dateField])) {
                    $data[$dateField] = null;
                }
            }
            $invoiceModel->update($id, $data);
            header('Location: /invoices/show/' . $id);
            exit;
        }
        header('Location: /invoices');
        exit;
    }

    public function delete($id) {
        $invoiceModel = new Invoice();
        $invoiceModel->delete($id);
        header('Location: /invoices');
        exit;
    }
} 