<?php

namespace App\Core;

class Router {
    private $routes = [];
    private $params = [];
    private $notFoundCallback;

    public function add($method, $route, $controller, $action) {
        $route = trim($route, '/');
        $route = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^/]+)', $route);
        $route = "#^" . $route . "$#";
        
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
        file_put_contents(__DIR__ . '/../../logs/debug.log', "MATCH: url=$url, method=$method\n", FILE_APPEND);
        $url = trim($url, '/');
        
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $params) {
                file_put_contents(__DIR__ . '/../../logs/debug.log', "Trying route: $route\n", FILE_APPEND);
                if (preg_match($route, $url, $matches)) {
                    file_put_contents(__DIR__ . '/../../logs/debug.log', "Matched: $route\n", FILE_APPEND);
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
        
        file_put_contents(__DIR__ . '/../../logs/debug.log', "No match found\n", FILE_APPEND);
        return false;
    }

    public function dispatch($url = null, $method = null) {
        $url = $url ?? $_SERVER['REQUEST_URI'];
        $method = $method ?? $_SERVER['REQUEST_METHOD'];
        file_put_contents(__DIR__ . '/../../logs/debug.log', "DISPATCH: url=$url, method=$method\n", FILE_APPEND);
        
        $url = parse_url($url, PHP_URL_PATH);
        
        // Отладка
        // Закомментированы все file_put_contents для debug.log
        
        if ($this->match($url, $method)) {
            $controller = "\\App\\Controllers\\" . $this->params['controller'];
            
            // Закомментированы все file_put_contents для debug.log
            
            if (class_exists($controller)) {
                // Закомментированы все file_put_contents для debug.log
                
                try {
                    $controllerObject = new $controller();
                    $action = $this->params['action'];
                    
                    // Закомментированы все file_put_contents для debug.log
                    
                    if (method_exists($controllerObject, $action)) {
                        // Закомментированы все file_put_contents для debug.log
                        
                        unset($this->params['controller'], $this->params['action']);
                        // Закомментированы все file_put_contents для debug.log
                        
                        call_user_func_array([$controllerObject, $action], array_values($this->params));
                        // Закомментированы все file_put_contents для debug.log
                        return;
                    } else {
                        // Закомментированы все file_put_contents для debug.log
                    }
                } catch (\Exception $e) {
                    // Закомментированы все file_put_contents для debug.log
                    
                    // Вывести ошибку на экран
                    echo '<div style="background-color: #ffeeee; border: 1px solid #ff0000; padding: 10px; margin: 10px;">';
                    echo '<h1>Ошибка маршрутизации</h1>';
                    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                    echo '</div>';
                    return;
                }
            } else {
                // Закомментированы все file_put_contents для debug.log
            }
        } else {
            // Закомментированы все file_put_contents для debug.log
        }
        
        if ($this->notFoundCallback) {
            // Закомментированы все file_put_contents для debug.log
            
            try {
                $controller = "\\App\\Controllers\\" . $this->notFoundCallback['controller'];
                $controllerObject = new $controller();
                $action = $this->notFoundCallback['action'];
                call_user_func([$controllerObject, $action]);
            } catch (\Exception $e) {
                // Закомментированы все file_put_contents для debug.log
                
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