<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Invoice;
use App\Models\LegalEntity;
use App\Models\InvoiceItem;
use App\Models\InvoiceFile;
use App\Models\Operation;

class InvoicesController extends Controller {
    private function requireAuth() {
        if (empty($_SESSION['id'])) {
            header('Location: /login');
            exit;
        }
    }

    public function index() {
        $this->requireAuth();
        $invoiceModel = new Invoice();
        $filters = $_GET;
        $invoices = $invoiceModel->getAll($filters);
        $this->view->render('invoices/index', [
            'invoices' => $invoices,
            'filters' => $filters
        ]);
    }

    public function show($id) {
        $this->requireAuth();
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
        $this->requireAuth();
        $legalModel = new LegalEntity();
        $legalEntities = $legalModel->getAll();
        $this->view->render('invoices/create', [
            'legalEntities' => $legalEntities
        ]);
    }

    public function store() {
        $this->requireAuth();
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
        $this->requireAuth();
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
        $this->requireAuth();
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
        $this->requireAuth();
        $invoiceModel = new Invoice();
        $invoiceModel->delete($id);
        header('Location: /invoices');
        exit;
    }

    // Генерация html для секции файлов по типу
    private function renderFilesHtml($invoice_id, $file_type) {
        $fileModel = new \App\Models\InvoiceFile();
        $files = $fileModel->getByType($invoice_id, $file_type);
        ob_start();
        foreach ($files as $file): ?>
            <div class="mb-2 d-flex align-items-center">
                <?php if (preg_match('/\.(jpg|jpeg|png)$/i', $file['file_name'])): ?>
                    <a href="/uploads/<?= htmlspecialchars($file['file_path']) ?>" target="_blank">
                        <img src="/uploads/<?= htmlspecialchars($file['file_path']) ?>" alt="" style="max-width: 80px; max-height: 80px; border:1px solid #ccc; margin-right:8px;">
                    </a>
                <?php elseif (preg_match('/\.pdf$/i', $file['file_name'])): ?>
                    <a href="/uploads/<?= htmlspecialchars($file['file_path']) ?>" target="_blank">
                        <i class="bi bi-file-earmark-pdf" style="font-size:2rem;color:#b00;"></i> PDF
                    </a>
                <?php endif; ?>
                <form class="delete-file-form ms-2" data-file-id="<?= $file['id'] ?>" data-invoice-id="<?= $invoice_id ?>" method="post">
                    <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                </form>
            </div>
        <?php endforeach;
        return ob_get_clean();
    }

    public function uploadFile($invoice_id) {
        $this->requireAuth();
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && isset($_POST['file_type'])) {
            $fileType = $_POST['file_type'];
            $allowedTypes = ['invoice', 'email', 'waybill'];
            if (!in_array($fileType, $allowedTypes)) {
                $msg = 'Недопустимый тип файла.';
                if ($isAjax) {
                    echo json_encode(['success' => false, 'error' => $msg]); exit;
                } else { echo $msg; exit; }
            }
            $file = $_FILES['file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $msg = 'Ошибка загрузки файла: ' . $file['error'];
                if ($isAjax) {
                    echo json_encode(['success' => false, 'error' => $msg]); exit;
                } else { echo $msg; exit; }
            }
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
            if (!in_array($ext, $allowedExt)) {
                $msg = 'Разрешены только jpg, png, pdf.';
                if ($isAjax) {
                    echo json_encode(['success' => false, 'error' => $msg]); exit;
                } else { echo $msg; exit; }
            }
            $uploadDir = __DIR__ . '/../../public/uploads/invoices/' . intval($invoice_id) . '/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    $msg = 'Не удалось создать папку для загрузки.';
                    if ($isAjax) {
                        echo json_encode(['success' => false, 'error' => $msg]); exit;
                    } else { echo $msg; exit; }
                }
            }
            $fileName = uniqid() . '-' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file['name']);
            $filePath = $uploadDir . $fileName;
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                $msg = 'Не удалось сохранить файл.';
                if ($isAjax) {
                    echo json_encode(['success' => false, 'error' => $msg]); exit;
                } else { echo $msg; exit; }
            }
            $relativePath = 'invoices/' . intval($invoice_id) . '/' . $fileName;
            $fileModel = new InvoiceFile();
            $fileModel->add($invoice_id, $relativePath, $fileType);
            if ($isAjax) {
                $html = $this->renderFilesHtml($invoice_id, $fileType);
                echo json_encode(['success' => true, 'html' => $html]); exit;
            } else {
                header('Location: /invoices/show/' . $invoice_id); exit;
            }
        }
        $msg = 'Некорректный запрос.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $msg]); exit;
        } else { echo $msg; exit; }
    }

    public function deleteFile($invoice_id, $file_id) {
        $this->requireAuth();
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        $fileModel = new InvoiceFile();
        $files = $fileModel->getByInvoice($invoice_id);
        $file = null;
        foreach ($files as $f) {
            if ($f['id'] == $file_id) {
                $file = $f;
                break;
            }
        }
        if ($file) {
            $fullPath = __DIR__ . '/../../public/uploads/' . $file['file_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            $fileModel->delete($file_id);
            if ($isAjax) {
                echo json_encode(['success' => true]); exit;
            } else {
                header('Location: /invoices/show/' . $invoice_id); exit;
            }
        }
        $msg = 'Файл не найден.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $msg]); exit;
        } else { echo $msg; exit; }
    }
} 