<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\LegalEntity;

class LegalEntitiesController extends Controller {
    public function index() {
        $model = new LegalEntity();
        $entities = $model->getAll();
        $this->view->render('legal_entities/index', [
            'entities' => $entities
        ]);
    }

    public function create() {
        $this->view->render('legal_entities/create');
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new LegalEntity();
            $model->create($_POST);
            header('Location: /legal-entities');
            exit;
        }
        header('Location: /legal-entities');
        exit;
    }

    public function edit($id) {
        $model = new LegalEntity();
        $entity = $model->getById($id);
        $this->view->render('legal_entities/edit', [
            'entity' => $entity
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new LegalEntity();
            $model->update($id, $_POST);
            header('Location: /legal-entities');
            exit;
        }
        header('Location: /legal-entities');
        exit;
    }

    public function delete($id) {
        $model = new LegalEntity();
        $model->delete($id);
        header('Location: /legal-entities');
        exit;
    }
} 