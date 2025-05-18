<?php

// Загружаем конфигурацию
$config = require __DIR__ . '/app/Config/config.php';

// Подключаемся к базе данных напрямую
try {
    $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['database']};charset={$config['db']['charset']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    $pdo = new PDO(
        $dsn,
        $config['db']['user'],
        $config['db']['password'],
        $options
    );

    echo "Подключение к базе данных выполнено успешно.\n\n";
    
    // Получаем информацию о полях таблиц
    $stmt = $pdo->query("SHOW COLUMNS FROM permissions");
    echo "Структура таблицы permissions:\n";
    foreach ($stmt->fetchAll() as $col) {
        echo $col['Field'] . " - " . $col['Type'] . "\n";
    }
    
    echo "\n";

    // Получаем все права
    $stmt = $pdo->query("SELECT id, name, slug, description FROM permissions");
    $permissions = $stmt->fetchAll();

    echo "Найдено " . count($permissions) . " записей прав доступа.\n";
    echo "Исправляем кодировку данных...\n\n";

    // Функция для преобразования текста
    function fixEncoding($text) {
        // Сначала пробуем определить кодировку
        $encodings = ['CP1251', 'KOI8-R', 'UTF-8'];
        $detectedEncoding = mb_detect_encoding($text, $encodings, true);
        
        if ($detectedEncoding === false) {
            $detectedEncoding = 'CP1251'; // По умолчанию предполагаем Windows-1251
        }
        
        if ($detectedEncoding !== 'UTF-8') {
            return mb_convert_encoding($text, 'UTF-8', $detectedEncoding);
        }
        
        return $text;
    }

    // Экранирование одинарных кавычек для SQL
    function escapeString($pdo, $str) {
        return str_replace("'", "''", $str);
    }

    // Прямые SQL запросы вместо подготавливаемых запросов
    foreach ($permissions as $permission) {
        $id = $permission['id'];
        $name = fixEncoding($permission['name']);
        $slug = $permission['slug']; // slug не меняем
        $description = fixEncoding($permission['description']);
        
        // Безопасно экранируем данные
        $name = escapeString($pdo, $name);
        $description = escapeString($pdo, $description);
        
        // Выводим текущие и новые значения
        echo "ID: {$id}\n";
        echo "  Старое имя: {$permission['name']}\n";
        echo "  Новое имя: {$name}\n";
        
        // Используем прямой SQL-запрос для обновления
        $sql = "UPDATE permissions SET name = '{$name}', description = '{$description}' WHERE id = {$id}";
        $pdo->exec($sql);
        
        echo "  Запись обновлена\n\n";
    }

    // Исправляем группы прав
    $stmt = $pdo->query("SELECT id, name, description FROM permission_groups");
    $groups = $stmt->fetchAll();

    echo "\nНайдено " . count($groups) . " записей групп прав доступа.\n";
    echo "Исправляем кодировку данных...\n\n";

    foreach ($groups as $group) {
        $id = $group['id'];
        $name = fixEncoding($group['name']);
        $description = fixEncoding($group['description']);
        
        // Безопасно экранируем данные
        $name = escapeString($pdo, $name);
        $description = escapeString($pdo, $description);
        
        echo "ID: {$id}\n";
        echo "  Старое имя: {$group['name']}\n";
        echo "  Новое имя: {$name}\n";
        
        $sql = "UPDATE permission_groups SET name = '{$name}', description = '{$description}' WHERE id = {$id}";
        $pdo->exec($sql);
        
        echo "  Запись обновлена\n\n";
    }

    echo "\nПроверяем результаты...\n";
    
    // Проверка результатов
    $stmt = $pdo->query("SELECT id, name, slug, description FROM permissions LIMIT 5");
    $permissions = $stmt->fetchAll();

    echo "\nПервые 5 записей из таблицы permissions после обновления:\n";
    foreach ($permissions as $permission) {
        echo "ID: {$permission['id']}, Name: {$permission['name']}, Slug: {$permission['slug']}\n";
        echo "Desc: {$permission['description']}\n\n";
    }

    echo "\nКодировка данных исправлена успешно.\n";

} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
} 