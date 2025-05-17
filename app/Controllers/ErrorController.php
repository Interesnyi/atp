<?php

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller {
    public function notFound() {
        header("HTTP/1.0 404 Not Found");
        return $this->view->render('error/404', [
            'title' => '404 - Страница не найдена'
        ]);
    }
} 