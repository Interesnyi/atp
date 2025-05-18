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
        $this->middlewares = [];
        
        // Загружаем права пользователя
        $userPermissions = $this->loadUserPermissions();
        
        // Передаем права в шаблон для использования в навигации
        $this->view->setGlobalData([
            'userPermissions' => $userPermissions
        ]);
    }

    /**
     * Добавляет middleware для контроллера или конкретного метода
     * @param string $middleware Тип middleware (auth, permission)
     * @param mixed $options Дополнительные параметры
     */
    protected function middleware($type, $options = []) {
        switch ($type) {
            case 'auth':
                // Проверка авторизации
                if (!isset($_SESSION['id']) && !isset($_SESSION['user_id'])) {
                    header('Location: /login');
                    exit;
                }
                break;
                
            case 'role':
                // Проверка роли пользователя
                if (!isset($_SESSION['role'])) {
                    header('Location: /login');
                    exit;
                }
                
                $allowedRoles = is_array($options) ? $options : [$options];
                
                if (!in_array($_SESSION['role'], $allowedRoles)) {
                    $this->renderForbidden('У вас нет доступа к этому разделу');
                    exit;
                }
                break;
                
            case 'permission':
                // Проверка конкретного права доступа
                if (!isset($_SESSION['role'])) {
                    header('Location: /login');
                    exit;
                }
                
                $requiredPermission = $options;
                
                // Администратор имеет доступ ко всему
                if ($_SESSION['role'] === 'admin') {
                    break;
                }
                
                // Для остальных проверяем наличие права
                $permissionModel = new \App\Models\Permission();
                $hasPermission = $permissionModel->hasPermission($_SESSION['role'], $requiredPermission);
                
                if (!$hasPermission) {
                    $this->renderForbidden('У вас нет необходимых прав для выполнения этого действия');
                    exit;
                }
                break;
        }
    }
    
    /**
     * Отображение страницы 403 с сообщением
     */
    protected function renderForbidden($message = 'Доступ запрещен') {
        header("HTTP/1.0 403 Forbidden");
        $this->view('error/403', [
            'title' => '403 - Доступ запрещен',
            'message' => $message
        ]);
        exit;
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

    /**
     * Загружает права пользователя и передает их в шаблон
     */
    protected function loadUserPermissions() {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];
            
            // Для админа не нужно загружать права, он имеет доступ ко всему
            if ($role === 'admin') {
                return [];
            }
            
            // Загружаем права для текущей роли
            $permissionModel = new \App\Models\Permission();
            $permissions = $permissionModel->getPermissionsByRole($role);
            
            // Формируем ассоциативный массив для быстрой проверки
            $userPermissions = [];
            foreach ($permissions as $permission) {
                $userPermissions[$permission['slug']] = true;
            }
            
            return $userPermissions;
        }
        
        return [];
    }

    /**
     * Проверяет, есть ли у текущего пользователя заданное право
     */
    protected function hasPermission($slug) {
        if (!isset($_SESSION['role'])) {
            return false;
        }
        
        // Администратор имеет все права
        if ($_SESSION['role'] === 'admin') {
            return true;
        }
        
        $permissionModel = new \App\Models\Permission();
        return $permissionModel->hasPermission($_SESSION['role'], $slug);
    }
} 