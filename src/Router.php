<?php

class Router {
    private $routes = [];
    private $notFoundCallback;

    /**
     * Добавляет маршрут в роутер
     * @param string $method HTTP метод (GET, POST и т.д.)
     * @param string $pattern URL паттерн
     * @param callable $callback Функция-обработчик
     */
    public function addRoute($method, $pattern, $callback) {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }

    /**
     * Добавляет обработчик для GET запросов
     * @param string $pattern URL паттерн
     * @param callable $callback Функция-обработчик
     */
    public function get($pattern, $callback) {
        $this->addRoute('GET', $pattern, $callback);
    }

    /**
     * Добавляет обработчик для POST запросов
     * @param string $pattern URL паттерн
     * @param callable $callback Функция-обработчик
     */
    public function post($pattern, $callback) {
        $this->addRoute('POST', $pattern, $callback);
    }

    /**
     * Устанавливает обработчик для 404 ошибки
     * @param callable $callback Функция-обработчик
     */
    public function setNotFoundHandler($callback) {
        $this->notFoundCallback = $callback;
    }

    /**
     * Обрабатывает входящий запрос
     * @param string $method HTTP метод
     * @param string $uri Запрошенный URI
     * @return mixed Результат обработки запроса
     */
    public function dispatch($method = null, $uri = null) {
        $method = $method ?? $_SERVER['REQUEST_METHOD'];
        $uri = $uri ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Удаляем завершающий слеш, если он есть
        $uri = rtrim($uri, '/');
        
        // Если URI пустой, устанавливаем его как корневой
        if (empty($uri)) {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $route['pattern'];
            
            // Преобразуем паттерн в регулярное выражение
            $pattern = preg_replace('/\{([^}]+)\}/', '(?P<\1>[^/]+)', $pattern);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                // Удаляем числовые ключи из массива совпадений
                $params = array_filter($matches, function($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                // Вызываем обработчик с параметрами
                return call_user_func_array($route['callback'], [$params]);
            }
        }

        // Если маршрут не найден, вызываем обработчик 404
        if ($this->notFoundCallback) {
            return call_user_func($this->notFoundCallback);
        }

        // Если обработчик 404 не установлен, возвращаем стандартную страницу 404
        header("HTTP/1.0 404 Not Found");
        return '404 Not Found';
    }
} 