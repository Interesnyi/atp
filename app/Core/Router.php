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
        
        if ($this->match($url, $method)) {
            $controller = "\\App\\Controllers\\" . $this->params['controller'];
            if (class_exists($controller)) {
                $controllerObject = new $controller();
                $action = $this->params['action'];
                
                if (method_exists($controllerObject, $action)) {
                    unset($this->params['controller'], $this->params['action']);
                    return call_user_func_array([$controllerObject, $action], [$this->params]);
                }
            }
        }
        
        if ($this->notFoundCallback) {
            $controller = "\\App\\Controllers\\" . $this->notFoundCallback['controller'];
            $controllerObject = new $controller();
            $action = $this->notFoundCallback['action'];
            return call_user_func([$controllerObject, $action]);
        }
        
        header("HTTP/1.0 404 Not Found");
        return '404 Not Found';
    }
} 