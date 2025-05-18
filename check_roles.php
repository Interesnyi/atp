<?php

// Загружаем автозагрузчик composer
require_once __DIR__ . '/vendor/autoload.php';

// Включаем отображение ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Запускаем сессию
session_start();

// Устанавливаем роль для проверки
$_SESSION['role'] = 'manager';

// Создаем тестовый класс, имитирующий контроллер
class TestController extends \App\Core\Controller {
    public function testAccess() {
        try {
            echo "<h1>Тест доступа к правам</h1>";
            
            // Получаем все права для роли
            $permissions = $this->loadUserPermissions();
            
            echo "<h2>Права для роли {$_SESSION['role']}:</h2>";
            echo "<pre>";
            print_r($permissions);
            echo "</pre>";
            
            // Проверяем работу hasPermission
            echo "<h2>Проверка конкретных прав:</h2>";
            $testPermissions = [
                'users.view',
                'users.edit',
                'maslosklad.access',
                'dashboard.access'
            ];
            
            foreach ($testPermissions as $perm) {
                $result = $this->hasPermission($perm) ? 'Да' : 'Нет';
                echo "Право '{$perm}': {$result}<br>";
            }
            
            // Проверяем доступ к базе данных через модель
            echo "<h2>Проверка модели Permission:</h2>";
            $permissionModel = new \App\Models\Permission();
            $allPermissions = $permissionModel->getAllPermissions();
            
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Имя</th><th>Слаг</th><th>Группа</th></tr>";
            
            foreach ($allPermissions as $perm) {
                echo "<tr>";
                echo "<td>{$perm['id']}</td>";
                echo "<td>{$perm['name']}</td>";
                echo "<td>{$perm['slug']}</td>";
                echo "<td>{$perm['group_name']}</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
        } catch (Exception $e) {
            echo "<div style='color: red;'>";
            echo "<h2>Ошибка:</h2>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<p>Файл: " . $e->getFile() . " (строка " . $e->getLine() . ")</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
        }
    }
}

// Создаем и запускаем тестовый контроллер
$controller = new TestController();
$controller->testAccess(); 