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

    // Пересоздаем структуру таблицы permissions с правильной кодировкой
    echo "Пересоздаем таблицы...\n";
    
    // Сохраняем связи role_permissions
    $stmt = $pdo->query("SELECT * FROM role_permissions");
    $role_permissions = $stmt->fetchAll();
    
    // Удаляем таблицы
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS role_permissions");
    $pdo->exec("DROP TABLE IF EXISTS permissions");
    $pdo->exec("DROP TABLE IF EXISTS permission_groups");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Создаем заново таблицу permission_groups
    $pdo->exec("CREATE TABLE `permission_groups` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
        `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Создаем заново таблицу permissions
    $pdo->exec("CREATE TABLE `permissions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
        `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
        `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `group_id` int(11) NOT NULL,
        `created_at` timestamp NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `group_id` (`group_id`),
        CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `permission_groups` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Создаем заново таблицу role_permissions
    $pdo->exec("CREATE TABLE `role_permissions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
        `permission_id` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `role_permission` (`role`,`permission_id`),
        KEY `permission_id` (`permission_id`),
        CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    echo "Таблицы успешно пересозданы.\n\n";
    
    // Добавляем группы прав
    $pdo->exec("INSERT INTO permission_groups (id, name, description) VALUES
        (1, 'Пользователи', 'Управление пользователями системы'),
        (2, 'Доступ', 'Доступ к модулям системы'),
        (3, 'Справочники', 'Управление справочниками')
    ");
    
    // Добавляем права
    $pdo->exec("INSERT INTO permissions (id, name, slug, description, group_id) VALUES
        (1, 'Просмотр пользователей', 'users.view', 'Просмотр списка пользователей', 1),
        (2, 'Создание пользователей', 'users.create', 'Создание новых пользователей', 1),
        (3, 'Редактирование пользователей', 'users.edit', 'Редактирование существующих пользователей', 1),
        (4, 'Удаление пользователей', 'users.delete', 'Удаление пользователей', 1),
        (5, 'Доступ к дашборду', 'dashboard.access', 'Доступ к панели управления', 2),
        (6, 'Доступ к складу масел', 'maslosklad.access', 'Доступ к модулю складского учета масел', 2),
        (7, 'Управление ролями', 'roles.manage', 'Управление ролями и правами пользователей', 1)
    ");
    
    // Восстанавливаем связи role_permissions
    if (count($role_permissions) > 0) {
        $values = [];
        foreach ($role_permissions as $rp) {
            $values[] = "('{$rp['role']}', {$rp['permission_id']})";
        }
        
        $sql = "INSERT INTO role_permissions (role, permission_id) VALUES " . implode(", ", $values);
        $pdo->exec($sql);
        
        echo "Восстановлены связи ролей с правами.\n";
    }
    
    echo "\nПроверка результатов:\n";
    
    // Проверяем результаты
    $stmt = $pdo->query("SELECT p.id, p.name, p.slug, p.description, pg.name as group_name 
                        FROM permissions p
                        JOIN permission_groups pg ON p.group_id = pg.id
                        ORDER BY p.id");
    $permissions = $stmt->fetchAll();
    
    foreach ($permissions as $permission) {
        echo "ID: {$permission['id']}, Name: {$permission['name']}, Slug: {$permission['slug']}\n";
        echo "Group: {$permission['group_name']}\n";
        echo "Description: {$permission['description']}\n\n";
    }
    
    echo "\nОперация успешно завершена.\n";
    
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
} 