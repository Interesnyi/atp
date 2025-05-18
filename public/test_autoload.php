<?php
// Выводим все ошибки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Подключаем автозагрузчик
require_once __DIR__ . '/../vendor/autoload.php';

// Выводим информацию о загруженных классах
echo "<h1>Проверка автозагрузки</h1>";

// Пробуем загрузить контроллеры
try {
    echo "<h2>Проверка наличия контроллеров:</h2>";
    
    $controllerClasses = [
        "\\App\\Controllers\\HomeController",
        "\\App\\Controllers\\AuthController",
        "\\App\\Controllers\\DashboardController",
        "\\App\\Controllers\\ErrorController"
    ];
    
    foreach ($controllerClasses as $class) {
        if (class_exists($class)) {
            echo "<div style='color:green'>Класс {$class} существует</div>";
        } else {
            echo "<div style='color:red'>Класс {$class} НЕ существует</div>";
        }
    }
    
    // Пробуем создать экземпляр роутера
    echo "<h2>Проверка роутера:</h2>";
    $router = new \App\Core\Router();
    echo "<div style='color:green'>Экземпляр роутера создан успешно</div>";
    
    // Проверяем доступ к файловой системе
    echo "<h2>Проверка прав доступа к директориям:</h2>";
    $directories = [
        __DIR__ . '/../app/Controllers',
        __DIR__ . '/../app/Models',
        __DIR__ . '/../app/Views',
        __DIR__ . '/../vendor'
    ];
    
    foreach ($directories as $dir) {
        if (is_readable($dir)) {
            echo "<div>Директория {$dir} доступна для чтения</div>";
        } else {
            echo "<div style='color:red'>Директория {$dir} НЕ доступна для чтения</div>";
        }
    }

} catch (Exception $e) {
    echo "<div style='color:red'>Ошибка: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<div style='color:red'>Фатальная ошибка: " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} 