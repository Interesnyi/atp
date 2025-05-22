<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Helpers\EncodingHelper;

// Настройка отображения ошибок - включаем для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Логирование ошибок в файл
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Создаем директорию для логов, если её нет
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0777, true);
}

// Отладочная функция
function debug_log($message) {
    file_put_contents(__DIR__ . '/../logs/debug.log', date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Устанавливаем правильную кодировку UTF-8
EncodingHelper::setUtf8Headers();

// Инициализация сессии
session_start();

// Логируем начало выполнения скрипта
debug_log('Начало выполнения index.php');

// Создаем экземпляр роутера
$router = new Router();

// Регистрируем маршруты
$router->get('', 'HomeController', 'index');

// Маршруты для дашборда
$router->get('dashboard', 'DashboardController', 'index');

// Маршруты для разделов системы
$router->get('balance', 'BalanceController', 'index');
$router->get('warehouses', 'WarehousesController', 'index');
$router->get('warehouses/manage', 'WarehousesController', 'warehouses');

// Маршруты по типам складов
$router->get('warehouses/material', 'WarehousesController', 'material');
$router->get('warehouses/tool', 'WarehousesController', 'tool');
$router->get('warehouses/oil', 'WarehousesController', 'oil');
$router->get('warehouses/autoparts', 'WarehousesController', 'autoparts');

// Маршруты для общих разделов
$router->get('warehouses/suppliers', 'WarehousesController', 'suppliers');
$router->get('warehouses/customers', 'WarehousesController', 'customers');
$router->get('warehouses/operations', 'OperationsController', 'index');
$router->get('warehouses/reports', 'WarehousesController', 'reports');
$router->get('warehouses/statistics', 'WarehousesController', 'statistics');

// Маршруты для управления складами
$router->post('warehouses/create-warehouse', 'WarehousesController', 'createWarehouse');
$router->post('warehouses/update-warehouse/{id}', 'WarehousesController', 'updateWarehouse');
$router->post('warehouses/delete-warehouse/{id}', 'WarehousesController', 'deleteWarehouse');

// Маршрут для просмотре и редактирования поставщиков
$router->get('warehouses/suppliers/view/{id}', 'WarehousesController', 'viewSupplier');
$router->get('warehouses/suppliers/edit/{id}', 'WarehousesController', 'editSupplier');
$router->get('warehouses/suppliers/create', 'WarehousesController', 'createSupplier');

// Маршруты для материального склада
$router->get('warehouses/material/{warehouseId}', 'MaterialWarehouseController', 'index');
$router->get('warehouses/material/{warehouseId}/item/{itemId}', 'MaterialWarehouseController', 'item');
$router->get('warehouses/material/{warehouseId}/reception', 'MaterialWarehouseController', 'reception');
$router->post('warehouses/material/{warehouseId}/process-reception', 'MaterialWarehouseController', 'processReception');
$router->get('warehouses/material/{warehouseId}/issue', 'MaterialWarehouseController', 'issue');
$router->post('warehouses/material/{warehouseId}/process-issue', 'MaterialWarehouseController', 'processIssue');
$router->get('warehouses/material/{warehouseId}/writeoff', 'MaterialWarehouseController', 'writeoff');
$router->post('warehouses/material/{warehouseId}/process-writeoff', 'MaterialWarehouseController', 'processWriteoff');
$router->get('warehouses/material/{warehouseId}/inventory', 'MaterialWarehouseController', 'inventory');
$router->post('warehouses/material/{warehouseId}/process-inventory', 'MaterialWarehouseController', 'processInventory');

// Маршруты для имущества
$router->get('warehouses/items', 'WarehousesController', 'items');
$router->get('warehouses/items/create', 'WarehousesController', 'createItem');
$router->post('warehouses/items/store', 'WarehousesController', 'storeItem');
$router->get('warehouses/items/edit/{id}', 'WarehousesController', 'editItem');
$router->post('warehouses/items/update/{id}', 'WarehousesController', 'updateItem');
$router->post('warehouses/items/delete/{id}', 'WarehousesController', 'deleteItem');
$router->get('warehouses/items/search', 'WarehousesController', 'searchItems');
$router->get('warehouses/items/categories', 'WarehousesController', 'itemCategories');
$router->get('warehouses/items/categories/create', 'WarehousesController', 'createItemCategory');
$router->post('warehouses/items/categories/store', 'WarehousesController', 'storeItemCategory');
$router->get('warehouses/items/categories/edit/{id}', 'WarehousesController', 'editItemCategory');
$router->post('warehouses/items/categories/update/{id}', 'WarehousesController', 'updateItemCategory');
$router->post('warehouses/items/categories/delete/{id}', 'WarehousesController', 'deleteItemCategory');
$router->get('warehouses/items/categories/search', 'WarehousesController', 'searchItemCategories');

// API маршруты
$router->get('api/items/by-category/{categoryId}', 'ApiController', 'itemsByCategory');
$router->get('api/items/{itemId}/inventory', 'ApiController', 'itemInventory');
$router->get('warehouses/api/suppliers/{id}', 'ApiController', 'getSupplier');
$router->post('warehouses/api/suppliers/create', 'ApiController', 'createSupplier');
$router->post('warehouses/api/suppliers/update/{id}', 'ApiController', 'updateSupplier');
$router->post('warehouses/api/suppliers/delete/{id}', 'ApiController', 'deleteSupplier');
$router->get('warehouses/update-supplier-table', 'WarehousesController', 'updateSupplierTable');
$router->get('warehouses/merge-suppliers', 'WarehousesController', 'mergeSuppliers');
$router->get('warehouses/api/buyers/{id}', 'ApiController', 'getBuyer');
$router->post('warehouses/api/buyers/create', 'ApiController', 'createBuyer');
$router->post('warehouses/api/buyers/update/{id}', 'ApiController', 'updateBuyer');
$router->post('warehouses/api/buyers/delete/{id}', 'ApiController', 'deleteBuyer');
$router->get('warehouses/api/buyers/search', 'ApiController', 'searchBuyers');

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

// Маршрут для получателей
$router->get('warehouses/buyers', 'BuyersController', 'index');
$router->get('warehouses/buyers/view/{id}', 'BuyersController', 'show');
$router->get('warehouses/buyers/edit/{id}', 'BuyersController', 'edit');

// Обработчик 404 ошибки
$router->setNotFoundHandler('ErrorController', 'notFound');

// Перед запуском роутера
debug_log('Запуск роутера с URI: ' . $_SERVER['REQUEST_URI']);

// Запускаем роутер
$router->dispatch(); 