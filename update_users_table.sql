-- Добавляем временные колонки
ALTER TABLE users ADD COLUMN username VARCHAR(100) NULL AFTER password;
ALTER TABLE users ADD COLUMN email VARCHAR(250) NULL AFTER username;
ALTER TABLE users ADD COLUMN role ENUM('admin', 'user', 'manager') DEFAULT 'user' AFTER email;
ALTER TABLE users ADD COLUMN created_at TIMESTAMP NULL AFTER role;
ALTER TABLE users ADD COLUMN updated_at TIMESTAMP NULL AFTER created_at;
ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL AFTER updated_at;

-- Заполняем временные колонки данными из существующих
UPDATE users SET 
    username = CONCAT(firstName, ' ', surName),
    email = loginEmail,
    created_at = NOW();

-- Делаем колонки NOT NULL после заполнения
ALTER TABLE users MODIFY COLUMN username VARCHAR(100) NOT NULL;
ALTER TABLE users MODIFY COLUMN email VARCHAR(250) NOT NULL;

-- Создаем уникальный индекс для email
ALTER TABLE users ADD UNIQUE INDEX idx_email (email);

-- Пользователю с ID=1 присваиваем роль admin
UPDATE users SET role = 'admin' WHERE id = 1; 