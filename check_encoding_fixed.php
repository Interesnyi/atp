<?php

// Загружаем модель Permission для проверки
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Permission;
use App\Core\Database;

// Включаем отображение ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<html><head>";
echo "<meta charset='utf-8'>";
echo "<title>Проверка кодировки</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    tr:nth-child(even) { background-color: #f9f9f9; }
</style>";
echo "</head><body>";

echo "<h1>Проверка кодировки базы данных</h1>";

try {
    // Получаем соединение с базой данных
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    // Проверяем настройки соединения
    echo "<h2>Настройки соединения с базой данных:</h2>";
    $vars = $connection->query("SHOW VARIABLES LIKE '%char%'");
    echo "<table><tr><th>Переменная</th><th>Значение</th></tr>";
    foreach ($vars->fetchAll(PDO::FETCH_KEY_PAIR) as $name => $value) {
        echo "<tr><td>{$name}</td><td>{$value}</td></tr>";
    }
    echo "</table>";
    
    // Проверяем таблицы
    echo "<h2>Проверка кодировки таблиц:</h2>";
    $tables = $connection->query("SHOW TABLE STATUS");
    echo "<table><tr><th>Таблица</th><th>Кодировка</th><th>Collation</th></tr>";
    foreach ($tables->fetchAll(PDO::FETCH_ASSOC) as $table) {
        echo "<tr><td>{$table['Name']}</td><td>{$table['Collation']}</td><td>{$table['Collation']}</td></tr>";
    }
    echo "</table>";
    
    // Создаем экземпляр модели Permission
    $permissionModel = new Permission();
    
    // Получаем все права
    echo "<h2>Права доступа (через модель Permission):</h2>";
    $permissions = $permissionModel->getAllPermissions();
    
    echo "<table><tr><th>ID</th><th>Имя</th><th>Слаг</th><th>Описание</th><th>Группа</th></tr>";
    foreach ($permissions as $permission) {
        echo "<tr>
            <td>{$permission['id']}</td>
            <td>{$permission['name']}</td>
            <td>{$permission['slug']}</td>
            <td>{$permission['description']}</td>
            <td>{$permission['group_name']}</td>
        </tr>";
    }
    echo "</table>";
    
    // Проверим группы
    echo "<h2>Группы прав доступа:</h2>";
    $groups = $permissionModel->getAllGroups();
    
    echo "<table><tr><th>ID</th><th>Имя</th><th>Описание</th></tr>";
    foreach ($groups as $group) {
        echo "<tr>
            <td>{$group['id']}</td>
            <td>{$group['name']}</td>
            <td>{$group['description']}</td>
        </tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'><h2>Ошибка:</h2>";
    echo "<p>{$e->getMessage()}</p>";
    echo "</div>";
}

echo "</body></html>"; 