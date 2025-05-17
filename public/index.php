<?php
require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../dbcon.php';

// Создаем экземпляр роутера
$router = new Router();

// Обработчик для главной страницы
$router->get('/', function() {
    require_once __DIR__ . '/../index.php';
    return '';
});

// Обработчик для страницы баланса
$router->get('/balance', function() {
    require_once __DIR__ . '/../balance/index.php';
    return '';
});

// Обработчик для страницы склада
$router->get('/maslosklad', function() {
    require_once __DIR__ . '/../maslosklad/index.php';
    return '';
});

// Обработчик для 404 ошибки
$router->setNotFoundHandler(function() {
    header("HTTP/1.0 404 Not Found");
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>404 - Страница не найдена</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center">
                    <h1 class="display-1">404</h1>
                    <h2>Страница не найдена</h2>
                    <p class="lead">Запрошенная страница не существует.</p>
                    <a href="/" class="btn btn-primary">Вернуться на главную</a>
                </div>
            </div>
        </div>
    </body>
    </html>';
});

// Запускаем роутер
echo $router->dispatch(); 