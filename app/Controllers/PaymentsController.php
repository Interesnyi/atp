<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payment;
use App\Models\Buyer;
use App\Models\LegalEntity;
use App\Models\Invoice;
use App\Models\PaymentInvoice;

class PaymentsController extends Controller
{
    public function index()
    {
        $paymentModel = new Payment();
        $filters = $_GET;
        $payments = $paymentModel->getAll($filters);
        $paymentInvoiceModel = new PaymentInvoice();
        $invoiceModel = new Invoice();
        // Для вывода номеров счетов и подсчёта итогов
        $totals = [];
        foreach ($payments as &$payment) {
            $paymentInvoices = $paymentInvoiceModel->where('payment_id = ?', [$payment['id']]);
            $invoiceNumbers = [];
            foreach ($paymentInvoices as $pi) {
                $invoice = $invoiceModel->getById($pi['invoice_id']);
                if ($invoice && !empty($invoice['number'])) {
                    $invoiceNumbers[] = $invoice['number'];
                }
            }
            $payment['invoice_numbers'] = $invoiceNumbers;
            // Группировка по Получатель-Плательщик
            $key = ($payment['legal_entity_name'] ?? '-') . ' — ' . ($payment['buyer_name'] ?? '-');
            if (!isset($totals[$key])) {
                $totals[$key] = 0;
            }
            $totals[$key] += $payment['amount'];
        }
        unset($payment);
        $this->view('payments/index', [
            'payments' => $payments,
            'totals' => $totals
        ]);
    }

    public function create()
    {
        $buyerModel = new Buyer();
        $legalEntityModel = new LegalEntity();
        $invoiceModel = new Invoice();
        $buyers = $buyerModel->all();
        $legalEntities = $legalEntityModel->all();
        $invoices = $invoiceModel->all();
        $this->view('payments/create', [
            'buyers' => $buyers,
            'legalEntities' => $legalEntities,
            'invoices' => $invoices
        ]);
    }

    public function store()
    {
        $paymentModel = new Payment();
        $paymentInvoiceModel = new PaymentInvoice();
        $data = [
            'payment_date' => $_POST['payment_date'],
            'buyer_id' => $_POST['buyer_id'],
            'legal_entity_id' => $_POST['legal_entity_id'],
            'amount' => $_POST['amount'],
            'comment' => $_POST['comment'] ?? null,
        ];
        $paymentId = $paymentModel->create($data);
        if (!empty($_POST['invoice_ids'])) {
            foreach ($_POST['invoice_ids'] as $invoiceId) {
                $paymentInvoiceModel->create([
                    'payment_id' => $paymentId,
                    'invoice_id' => $invoiceId
                ]);
            }
        }
        header('Location: /payments');
        exit;
    }

    public function edit($id)
    {
        $paymentModel = new Payment();
        $payment = $paymentModel->find($id);
        $buyerModel = new Buyer();
        $legalEntityModel = new LegalEntity();
        $invoiceModel = new Invoice();
        $buyers = $buyerModel->all();
        $legalEntities = $legalEntityModel->all();
        $invoices = $invoiceModel->all();
        $selectedInvoices = (new PaymentInvoice())->where('payment_id = ?', [$id]);
        $selectedInvoiceIds = array_map(function($pi) { return $pi['invoice_id']; }, $selectedInvoices);
        $this->view('payments/edit', [
            'payment' => $payment,
            'buyers' => $buyers,
            'legalEntities' => $legalEntities,
            'invoices' => $invoices,
            'selectedInvoices' => $selectedInvoiceIds
        ]);
    }

    public function update($id)
    {
        $paymentModel = new Payment();
        $paymentInvoiceModel = new PaymentInvoice();
        $data = [
            'payment_date' => $_POST['payment_date'],
            'buyer_id' => $_POST['buyer_id'],
            'legal_entity_id' => $_POST['legal_entity_id'],
            'amount' => $_POST['amount'],
            'comment' => $_POST['comment'] ?? null,
        ];
        $paymentModel->update($id, $data);
        // Удаляем все старые связи для этой оплаты
        $paymentInvoiceModel->deleteByPaymentId($id);
        // Добавляем новые связи
        if (!empty($_POST['invoice_ids'])) {
            foreach ($_POST['invoice_ids'] as $invoiceId) {
                $paymentInvoiceModel->create([
                    'payment_id' => $id,
                    'invoice_id' => $invoiceId
                ]);
            }
        }
        header('Location: /payments');
        exit;
    }

    public function show($id)
    {
        $paymentModel = new Payment();
        $buyerModel = new Buyer();
        $legalEntityModel = new LegalEntity();
        $payment = $paymentModel->getById($id);
        $buyer = $buyerModel->find($payment['buyer_id']);
        $legalEntity = $legalEntityModel->find($payment['legal_entity_id']);
        // Получаем связанные счета
        $paymentInvoiceModel = new PaymentInvoice();
        $invoiceModel = new Invoice();
        $paymentInvoices = $paymentInvoiceModel->where('payment_id = ?', [$id]);
        $invoices = [];
        foreach ($paymentInvoices as $pi) {
            $invoices[] = $invoiceModel->getById($pi['invoice_id']);
        }
        $this->view('payments/show', [
            'payment' => $payment,
            'buyer' => $buyer,
            'legalEntity' => $legalEntity,
            'invoices' => $invoices
        ]);
    }

    public function delete($id)
    {
        PaymentInvoice::where(['payment_id' => $id])->delete();
        Payment::delete($id);
        header('Location: /payments');
        exit;
    }
} 