<?php

// Параметры подключения к базе данных
$host = 'db';
$database = 'cardicom';
$user = 'root';
$password = 'root';

// Скрипт создания таблиц
$sqlScript = file_get_contents(__DIR__ . '/app/Config/migrations/create_warehouse_tables.sql');

try {
    // Разбиваем скрипт на отдельные SQL-запросы
    $queries = explode(';', $sqlScript);
    
    // Подключаемся к базе данных
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Выполняем каждый запрос
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            echo "Выполнение запроса: " . substr($query, 0, 50) . "...\n";
            $pdo->exec($query);
        }
    }
    
    echo "Таблицы складов успешно созданы и заполнены.\n";
    
    // Проверяем созданные таблицы
    echo "\nСписок типов складов:\n";
    $stmt = $pdo->query("SELECT * FROM warehouse_types");
    $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($types);
    
    echo "\nСписок складов:\n";
    $stmt = $pdo->query("SELECT * FROM warehouses");
    $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($warehouses);
    
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
} 