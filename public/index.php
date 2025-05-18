<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Helpers\EncodingHelper;

// Настройка отображения ошибок - включаем для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Устанавливаем правильную кодировку UTF-8
EncodingHelper::setUtf8Headers();

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
$router->get('maslosklad/warehouses', 'MasloskladController', 'warehouses');

// Маршруты для материального склада
$router->get('maslosklad/material/{warehouseId}', 'MaterialWarehouseController', 'index');
$router->get('maslosklad/material/{warehouseId}/item/{itemId}', 'MaterialWarehouseController', 'item');
$router->get('maslosklad/material/{warehouseId}/reception', 'MaterialWarehouseController', 'reception');
$router->post('maslosklad/material/{warehouseId}/process-reception', 'MaterialWarehouseController', 'processReception');
$router->get('maslosklad/material/{warehouseId}/issue', 'MaterialWarehouseController', 'issue');
$router->post('maslosklad/material/{warehouseId}/process-issue', 'MaterialWarehouseController', 'processIssue');
$router->get('maslosklad/material/{warehouseId}/writeoff', 'MaterialWarehouseController', 'writeoff');
$router->post('maslosklad/material/{warehouseId}/process-writeoff', 'MaterialWarehouseController', 'processWriteoff');
$router->get('maslosklad/material/{warehouseId}/inventory', 'MaterialWarehouseController', 'inventory');
$router->post('maslosklad/material/{warehouseId}/process-inventory', 'MaterialWarehouseController', 'processInventory');

// API маршруты
$router->get('api/items/by-category/{categoryId}', 'ApiController', 'itemsByCategory');
$router->get('api/items/{itemId}/inventory', 'ApiController', 'itemInventory');

// Маршруты для управления пользователями
$router->get('users', 'UsersController', 'index');
$router->get('users/create', 'UsersController', 'create');
$router->post('users/store', 'UsersController', 'store');
$router->get('users/show/{id}', 'UsersController', 'show');
$router->get('users/edit/{id}', 'UsersController', 'edit');
$router->post('users/update/{id}', 'UsersController', 'update');
$router->get('users/delete/{id}', 'UsersController', 'delete');

// Маршруты для управления ролями
$router->get('roles', 'RolesController', 'index');
$router->get('roles/permissions/{role}', 'RolesController', 'permissions');
$router->post('roles/permissions/{role}', 'RolesController', 'permissions');

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
$router->dispatch(); 