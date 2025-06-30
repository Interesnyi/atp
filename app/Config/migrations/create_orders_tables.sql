-- Заказчики
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(32),
    email VARCHAR(128),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Автомобили
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    brand VARCHAR(64) NOT NULL,
    model VARCHAR(64) NOT NULL,
    year INT,
    vin VARCHAR(64),
    license_plate VARCHAR(32),
    body_number VARCHAR(64),
    engine_number VARCHAR(64),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- Заказ-наряды
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    car_id INT NOT NULL,
    order_number VARCHAR(32) NOT NULL,
    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_completed DATETIME,
    manager VARCHAR(128),
    status VARCHAR(32) DEFAULT 'new',
    comment TEXT,
    contract_id INT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    FOREIGN KEY (contract_id) REFERENCES contracts(id) ON DELETE SET NULL
);

-- Работы по заказу
CREATE TABLE IF NOT EXISTS order_works (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    work_type_id INT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(32),
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) DEFAULT 0,
    executor VARCHAR(128),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Материалы исполнителя (со склада)
CREATE TABLE IF NOT EXISTS order_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(32),
    quantity DECIMAL(10,2) DEFAULT 1,
    price DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Материалы заказчика
CREATE TABLE IF NOT EXISTS order_customer_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    customer_material_id INT,
    name VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Справочник работ
CREATE TABLE IF NOT EXISTS work_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(32),
    price DECIMAL(10,2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Справочник материалов заказчика
CREATE TABLE IF NOT EXISTS customer_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Файлы к заказ-наряду
CREATE TABLE IF NOT EXISTS order_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(64),
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Категории работ
CREATE TABLE IF NOT EXISTS work_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Добавить поле category_id в work_types
ALTER TABLE work_types ADD COLUMN category_id INT NULL AFTER id;
ALTER TABLE work_types ADD CONSTRAINT fk_work_types_category FOREIGN KEY (category_id) REFERENCES work_categories(id) ON DELETE SET NULL; 