-- Удаляем таблицу role_permissions (сначала, так как она имеет зависимость)
DROP TABLE IF EXISTS role_permissions;

-- Удаляем таблицу permissions
DROP TABLE IF EXISTS permissions;

-- Создаем заново таблицу permissions с правильной структурой
CREATE TABLE permissions (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    group_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_slug (slug),
    FOREIGN KEY (group_id) REFERENCES permission_groups(id) ON DELETE CASCADE
);

-- Создаем заново таблицу role_permissions
CREATE TABLE role_permissions (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    role ENUM('admin', 'user', 'manager') NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role_permission (role, permission_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- Добавляем базовые группы разрешений, если их еще нет
INSERT IGNORE INTO permission_groups (name, slug, description) VALUES
('Управление пользователями', 'users', 'Права на управление учетными записями пользователей'),
('Доступ к разделам', 'sections', 'Права на доступ к различным разделам системы'),
('Управление справочниками', 'dictionaries', 'Права на управление справочными данными');

-- Добавляем базовые разрешения для управления пользователями
INSERT INTO permissions (name, slug, description, group_id) VALUES
('Просмотр пользователей', 'users.view', 'Просмотр списка пользователей', 1),
('Создание пользователей', 'users.create', 'Создание новых пользователей', 1),
('Редактирование пользователей', 'users.edit', 'Редактирование существующих пользователей', 1),
('Удаление пользователей', 'users.delete', 'Удаление пользователей', 1);

-- Добавляем базовые разрешения для доступа к разделам
INSERT INTO permissions (name, slug, description, group_id) VALUES
('Доступ к дашборду', 'dashboard.access', 'Доступ к панели управления', 2),
('Доступ к складу масел', 'maslosklad.access', 'Доступ к модулю склада масел', 2),
('Доступ к финансам', 'balance.access', 'Доступ к финансовому модулю', 2);

-- Назначаем все права пользователям с ролью admin
INSERT INTO role_permissions (role, permission_id)
SELECT 'admin', id FROM permissions;

-- Назначаем базовые права пользователям с ролью user
INSERT INTO role_permissions (role, permission_id)
SELECT 'user', id FROM permissions WHERE slug IN ('dashboard.access', 'maslosklad.access'); 