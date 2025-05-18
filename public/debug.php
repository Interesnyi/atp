<?php
// Отображение всех ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Отладочная страница</h1>";

// Информация о PHP
echo "<h2>Информация о PHP</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Loaded Extensions: " . implode(", ", get_loaded_extensions()) . "\n";
echo "</pre>";

// Тестовое подключение к БД
echo "<h2>Тест подключения к базе данных</h2>";
try {
    $dsn = "mysql:host=db;dbname=cardicom;charset=utf8mb4";
    $pdo = new PDO($dsn, 'cardicom_user', 'cardicom_pass', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<div style='color:green'>Подключение к БД успешно</div>";
    
    // Проверка таблицы users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetchColumn();
    echo "<div>Количество пользователей в БД: $userCount</div>";
    
    // Проверка таблицы permissions
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM permissions");
    $permissionCount = $stmt->fetchColumn();
    echo "<div>Количество записей прав доступа: $permissionCount</div>";
    
} catch (PDOException $e) {
    echo "<div style='color:red'>Ошибка БД: " . $e->getMessage() . "</div>";
}

// Информация о сессии
echo "<h2>Информация о сессии</h2>";
echo "<pre>";
session_start();
print_r($_SESSION);
echo "</pre>";

// Умышленная ошибка для проверки отображения ошибок
echo "<h2>Тест отображения ошибок</h2>";
try {
    $undefinedVar++;
} catch (Error $e) {
    echo "<div style='color:orange'>Перехваченная ошибка: " . $e->getMessage() . "</div>";
} 