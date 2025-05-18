<?php

// Загружаем конфигурацию
$config = require __DIR__ . '/app/Config/config.php';

// Выводим текущие настройки
echo "Настройки кодировки:\n";
echo "DB Charset: " . $config['db']['charset'] . "\n";
if (isset($config['db']['collation'])) {
    echo "DB Collation: " . $config['db']['collation'] . "\n";
}

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

    // Дополнительно устанавливаем кодировку соединения
    $pdo->exec("SET CHARACTER SET utf8mb4");
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("SET COLLATION_CONNECTION = 'utf8mb4_unicode_ci'");

    echo "Подключение к базе данных выполнено успешно.\n";

    // Выводим информацию о кодировке в базе данных
    $stmt = $pdo->query("SHOW VARIABLES LIKE 'char%'");
    $charsetVars = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    echo "\nНастройки кодировки MySQL:\n";
    foreach ($charsetVars as $name => $value) {
        echo "$name: $value\n";
    }

    // Проверяем данные в таблице permissions
    $stmt = $pdo->query("SELECT id, name, slug, description FROM permissions LIMIT 5");
    $permissions = $stmt->fetchAll();

    echo "\nДанные из таблицы permissions:\n";
    foreach ($permissions as $permission) {
        echo "ID: {$permission['id']}, Name: {$permission['name']}, Slug: {$permission['slug']}, Desc: {$permission['description']}\n";
    }

    // Устанавливаем правильную кодировку для таблицы permissions и permission_groups
    echo "\nИсправляем кодировку для таблиц...\n";
    $pdo->exec("ALTER TABLE permissions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE permission_groups CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE role_permissions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "Кодировка таблиц исправлена.\n";

} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
} 