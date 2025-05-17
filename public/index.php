<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

// Настройка отображения ошибок
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Инициализация сессии
session_start();

// Создаем экземпляр роутера
$router = new Router();

// Регистрируем маршруты
$router->get('', 'HomeController', 'index');

// Маршруты для дашборда
$router->get('dashboard', 'DashboardController', 'index');

// Маршруты для разделов системы
$router->get('balance', 'BalanceController', 'index');
$router->get('maslosklad', 'MasloskladController', 'index');

// Маршруты для профиля пользователя
$router->get('profile', 'ProfileController', 'index');
$router->post('profile/update', 'ProfileController', 'update');

// Маршруты для авторизации
$router->get('login', 'AuthController', 'index');
$router->post('login', 'AuthController', 'login');
$router->get('register', 'AuthController', 'registerForm');
$router->post('register', 'AuthController', 'register');
$router->get('logout', 'AuthController', 'logout');

// Обработчик 404 ошибки
$router->setNotFoundHandler('ErrorController', 'notFound');

// Запускаем роутер
echo $router->dispatch(); 