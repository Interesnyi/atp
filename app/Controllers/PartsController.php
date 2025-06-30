<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Part;

class PartsController extends Controller {
    public function index() {
        $parts = (new Part())->getAllParts();
        $this->view->render('parts/index', ['parts' => $parts, 'title' => 'Справочник запчастей']);
    }

    public function create() {
        $this->view->render('parts/create', ['title' => 'Добавить запчасть']);
    }

    public function store() {
        (new Part())->createPart($_POST);
        header('Location: /parts');
        exit;
    }

    public function edit($id) {
        $part = (new Part())->getPartById($id);
        $this->view->render('parts/edit', ['part' => $part, 'title' => 'Редактировать запчасть']);
    }

    public function update($id) {
        (new Part())->updatePart($id, $_POST);
        header('Location: /parts');
        exit;
    }

    public function delete($id) {
        (new Part())->deletePart($id);
        header('Location: /parts');
        exit;
    }
} 