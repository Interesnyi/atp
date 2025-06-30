<?php
namespace App\Controllers;

use App\Core\Controller;

class RepairsController extends Controller {
    public function index() {
        $this->view->render('repairs/index', [
            'title' => 'Ремонты'
        ]);
    }
} 