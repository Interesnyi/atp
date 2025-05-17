<?php
try {
    $pdo = new PDO(
        'mysql:host=db;dbname=cardicom',
        'cardicom_user',
        'cardicom_pass'
    );
    echo "Подключение к базе данных успешно установлено!\n";
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Таблицы в базе данных:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
} 