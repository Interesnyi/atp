-- Таблица групп прав доступа (для организации прав по разделам)
CREATE TABLE IF NOT EXISTS permission_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица прав доступа
CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES permission_groups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица связи ролей и прав доступа
CREATE TABLE IF NOT EXISTS role_permissions (
    role VARCHAR(50) NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (role, permission_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Заполнение групп прав доступа
INSERT INTO permission_groups (name, description) VALUES
('Пользователи', 'Управление пользователями системы'),
('Финансы', 'Работа с финансовым разделом'),
('Склад масел', 'Управление складом масел'),
('Панель управления', 'Доступ к панели управления');

-- Заполнение прав доступа
INSERT INTO permissions (group_id, name, slug, description) VALUES
-- Пользователи
(1, 'Просмотр пользователей', 'users.view', 'Просмотр списка пользователей'),
(1, 'Создание пользователей', 'users.create', 'Создание новых пользователей'),
(1, 'Редактирование пользователей', 'users.edit', 'Редактирование данных пользователей'),
(1, 'Удаление пользователей', 'users.delete', 'Удаление пользователей'),
-- Финансы
(2, 'Просмотр финансов', 'finance.view', 'Просмотр финансовых данных'),
(2, 'Создание финансовых записей', 'finance.create', 'Создание финансовых записей'),
(2, 'Редактирование финансов', 'finance.edit', 'Редактирование финансовых записей'),
(2, 'Удаление финансовых записей', 'finance.delete', 'Удаление финансовых записей'),
-- Склад масел
(3, 'Просмотр склада', 'storage.view', 'Просмотр данных склада масел'),
(3, 'Добавление товаров', 'storage.create', 'Добавление товаров на склад'),
(3, 'Редактирование товаров', 'storage.edit', 'Редактирование данных товаров'),
(3, 'Удаление товаров', 'storage.delete', 'Удаление товаров со склада'),
-- Панель управления
(4, 'Доступ к панели', 'dashboard.access', 'Доступ к панели управления'),
(4, 'Просмотр статистики', 'dashboard.stats', 'Просмотр статистики и аналитики');

-- Назначение прав для роли admin (все права)
INSERT INTO role_permissions (role, permission_id)
SELECT 'admin', id FROM permissions;

-- Назначение прав для роли manager (все, кроме удаления пользователей)
INSERT INTO role_permissions (role, permission_id)
SELECT 'manager', id FROM permissions WHERE slug != 'users.delete';

-- Назначение базовых прав для обычных пользователей
INSERT INTO role_permissions (role, permission_id)
SELECT 'user', id FROM permissions 
WHERE slug IN ('dashboard.access', 'storage.view', 'finance.view'); 