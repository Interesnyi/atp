<?php

namespace App\Core;

abstract class Controller {
    protected $view;
    protected $model;
    protected $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../Config/config.php';
        $this->view = new View();
    }

    protected function loadModel($model) {
        $modelClass = "\\App\\Models\\" . $model;
        if (class_exists($modelClass)) {
            return new $modelClass();
        }
        throw new \Exception("Model {$model} not found");
    }

    protected function view($name, $data = []) {
        return $this->view->render($name, $data);
    }

    protected function json($data, $statusCode = 200) {
        // Отключаем вывод предупреждений и ошибок
        if (headers_sent()) {
            // Если заголовки уже отправлены, просто выводим JSON
            echo json_encode($data);
        } else {
            // Если заголовки еще не отправлены, устанавливаем их
            header('Content-Type: application/json');
            http_response_code($statusCode);
            echo json_encode($data);
        }
        exit;
    }

    protected function redirect($url) {
        if (!headers_sent()) {
            header("Location: {$url}");
        } else {
            echo '<script>window.location.href="' . $url . '";</script>';
        }
        exit;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function getPost($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    protected function getQuery($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    protected function validateRequired($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                return false;
            }
        }
        return true;
    }

    protected function csrf() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCsrf($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
} 