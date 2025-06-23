<?php

namespace App\Controllers;

class FileController {
    private function requireAuth() {
        if (empty($_SESSION['id'])) {
            header('Location: /login');
            exit;
        }
    }

    public function invoiceFile($invoice_id, $file) {
        $this->requireAuth();
        $safeFile = basename($file); // защита от ../
        $path = __DIR__ . '/../../public/uploads/invoices/' . intval($invoice_id) . '/' . $safeFile;
        if (!file_exists($path)) {
            header('HTTP/1.0 404 Not Found');
            echo 'Файл не найден';
            exit;
        }
        $ext = strtolower(pathinfo($safeFile, PATHINFO_EXTENSION));
        $mime = 'application/octet-stream';
        if ($ext === 'pdf') $mime = 'application/pdf';
        if ($ext === 'jpg' || $ext === 'jpeg') $mime = 'image/jpeg';
        if ($ext === 'png') $mime = 'image/png';
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . $safeFile . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
} 