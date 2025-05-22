<?php

namespace App\Core;

class Router {
    private $routes = [];
    private $params = [];
    private $notFoundCallback;

    public function add($method, $route, $controller, $action) {
        $route = trim($route, '/');
        $route = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<\1>[^/]+)', $route);
        $route = "/^" . str_replace('/', '\/', $route) . "$/";
        
        $this->routes[$method][$route] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function get($route, $controller, $action) {
        $this->add('GET', $route, $controller, $action);
    }

    public function post($route, $controller, $action) {
        $this->add('POST', $route, $controller, $action);
    }

    public function setNotFoundHandler($controller, $action) {
        $this->notFoundCallback = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function match($url, $method) {
        $url = trim($url, '/');
        
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $params) {
                if (preg_match($route, $url, $matches)) {
                    foreach ($matches as $key => $match) {
                        if (is_string($key)) {
                            $params[$key] = $match;
                        }
                    }
                    $this->params = $params;
                    return true;
                }
            }
        }
        
        return false;
    }

    public function dispatch($url = null, $method = null) {
        $url = $url ?? $_SERVER['REQUEST_URI'];
        $method = $method ?? $_SERVER['REQUEST_METHOD'];
        
        $url = parse_url($url, PHP_URL_PATH);
        
        // Отладка
        file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - URL: {$url}, Method: {$method}" . PHP_EOL, FILE_APPEND);
        
        if ($this->match($url, $method)) {
            $controller = "\\App\\Controllers\\" . $this->params['controller'];
            
            file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - Найден маршрут. Контроллер: {$controller}" . PHP_EOL, FILE_APPEND);
            
            if (class_exists($controller)) {
                file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - Класс контроллера существует" . PHP_EOL, FILE_APPEND);
                
                try {
                    $controllerObject = new $controller();
                    $action = $this->params['action'];
                    
                    file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - Действие: {$action}" . PHP_EOL, FILE_APPEND);
                    
                    if (method_exists($controllerObject, $action)) {
                        file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - Метод существует, вызываем" . PHP_EOL, FILE_APPEND);
                        
                        unset($this->params['controller'], $this->params['action']);
                        file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - Параметры: " . json_encode($this->params) . PHP_EOL, FILE_APPEND);
                        
                        call_user_func_array([$controllerObject, $action], array_values($this->params));
                        file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - Действие выполнено" . PHP_EOL, FILE_APPEND);
                        return;
                    } else {
                        file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - ОШИБКА: Метод {$action} не существует в контроллере" . PHP_EOL, FILE_APPEND);
                    }
                } catch (\Exception $e) {
                    file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - ОШИБКА: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
                    file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Трассировка: " . $e->getTraceAsString() . PHP_EOL, FILE_APPEND);
                    
                    // Вывести ошибку на экран
                    echo '<div style="background-color: #ffeeee; border: 1px solid #ff0000; padding: 10px; margin: 10px;">';
                    echo '<h1>Ошибка маршрутизации</h1>';
                    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                    echo '</div>';
                    return;
                }
            } else {
                file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - ОШИБКА: Класс контроллера {$controller} не существует" . PHP_EOL, FILE_APPEND);
            }
        } else {
            file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - Маршрут не найден для URL: {$url}" . PHP_EOL, FILE_APPEND);
        }
        
        if ($this->notFoundCallback) {
            file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - Вызываем обработчик 404" . PHP_EOL, FILE_APPEND);
            
            try {
                $controller = "\\App\\Controllers\\" . $this->notFoundCallback['controller'];
                $controllerObject = new $controller();
                $action = $this->notFoundCallback['action'];
                call_user_func([$controllerObject, $action]);
            } catch (\Exception $e) {
                file_put_contents(__DIR__ . '/../../logs/debug.log', date('[Y-m-d H:i:s] ') . "Router::dispatch - ОШИБКА в обработчике 404: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
                
                // Вывести ошибку на экран
                echo '<div style="background-color: #ffeeee; border: 1px solid #ff0000; padding: 10px; margin: 10px;">';
                echo '<h1>Ошибка обработчика 404</h1>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                echo '</div>';
            }
            return;
        }
        
        header("HTTP/1.0 404 Not Found");
        echo '<div style="text-align: center; margin-top: 100px;">';
        echo '<h1>404 Not Found</h1>';
        echo '<p>Запрашиваемая страница не найдена</p>';
        echo '</div>';
    }
} 