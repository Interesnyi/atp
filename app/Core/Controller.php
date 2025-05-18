<?php

namespace App\Core;

abstract class Controller {
    protected $view;
    protected $model;
    protected $config;
    protected $middlewares = [];

    public function __construct() {
        $this->config = require __DIR__ . '/../Config/config.php';
        $this->view = new View();
    }

    /**
     * Добавляет middleware для контроллера или конкретного метода
     * @param string $middleware Тип middleware (auth, permission)
     * @param mixed $options Дополнительные параметры
     */
    protected function middleware($middleware, $options = null) {
        $this->middlewares[] = [
            'type' => $middleware,
            'options' => $options
        ];
        
        // Применяем middleware
        $this->applyMiddleware($middleware, $options);
    }
    
    /**
     * Применяет middleware к текущему запросу
     * @param string $middleware Тип middleware
     * @param mixed $options Дополнительные параметры
     */
    protected function applyMiddleware($middleware, $options = null) {
        switch ($middleware) {
            case 'auth':
                $this->authMiddleware();
                break;
        }
    }
    
    /**
     * Middleware для проверки авторизации
     */
    protected function authMiddleware() {
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['id'])) {
            // Сохраняем URL для редиректа после авторизации
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            
            // Перенаправляем на страницу входа
            $this->redirect('/login');
        }
    }

    protected function loadModel($model) {
        $modelClass = "\\App\\Models\\" . $model;
        if (class_exists($modelClass)) {
            return new $modelClass();
        }
        throw new \Exception("Model {$model} not found");
    }

    protected function view($name, $data = []) {
        // Получаем результат рендеринга и выводим его
        echo $this->view->render($name, $data);
        return true;
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

    /**
     * Отображение страницы 404 с сообщением
     * @param string $message Сообщение для отображения
     */
    protected function renderNotFound($message = 'Страница не найдена') {
        header("HTTP/1.0 404 Not Found");
        $this->view('error/404', [
            'title' => '404 - Страница не найдена',
            'message' => $message
        ]);
        exit;
    }
} 