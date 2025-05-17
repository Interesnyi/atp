<?php

namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller {

    public function __construct() {
        parent::__construct();
        // Проверка авторизации
        $this->checkAuth();
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['id'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        return $this->view('dashboard/index');
    }
} 