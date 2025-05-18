<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index() {
        // Если пользователь авторизован, перенаправляем на /maslosklad
        if (isset($_SESSION["id"]) && $_SESSION["id"] > 0) {
            $this->redirect('/maslosklad');
        }

        return $this->view('home/index', [
            'title' => 'ELDIR | Главная страница'
        ]);
    }
} 