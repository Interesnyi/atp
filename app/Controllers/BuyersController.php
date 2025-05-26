<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Buyer;

class BuyersController extends Controller {
    /**
     * Список получателей
     */
    public function index() {
        $buyerModel = new Buyer();
        $buyers = $buyerModel->getAllBuyers();
        $this->view->render('warehouses/buyers/index', [
            'buyers' => $buyers,
            'title' => 'Получатели'
        ]);
    }

    /**
     * Просмотр получателя
     */
    public function show($id) {
        $buyerModel = new Buyer();
        $buyer = $buyerModel->getBuyerById($id);
        if (!$buyer) {
            $this->view->renderError('Получатель не найден', 'Получатель с таким ID не найден.');
            return;
        }
        $this->view->render('warehouses/buyers/view', [
            'buyer' => $buyer,
            'title' => 'Просмотр получателя'
        ]);
    }

    /**
     * Страница создания нового получателя
     */
    public function create() {
        $this->view->render('warehouses/buyers/create', [
            'title' => 'Добавить получателя'
        ]);
    }

    /**
     * Редактирование получателя
     */
    public function edit($id) {
        $buyerModel = new Buyer();
        $buyer = $buyerModel->getBuyerById($id);
        if (!$buyer) {
            $this->view->renderError('Получатель не найден', 'Получатель с таким ID не найден.');
            return;
        }
        $this->view->render('warehouses/buyers/edit', [
            'buyer' => $buyer,
            'title' => 'Редактировать получателя'
        ]);
    }
} 