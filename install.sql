-- install.sql — PhoneStore database setup
-- Import via phpMyAdmin or: mysql -u root phonestore < install.sql

CREATE DATABASE IF NOT EXISTS phonestore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phonestore;

CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('user','admin') DEFAULT 'user',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(200) NOT NULL,
    brand       VARCHAR(100) NOT NULL,
    price       DECIMAL(10,2) NOT NULL,
    description TEXT,
    image       VARCHAR(500),
    stock       INT DEFAULT 100,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cart (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    product_id  INT NOT NULL,
    quantity    INT NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_product (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    total       DECIMAL(10,2) NOT NULL,
    status      ENUM('new','processing','shipped','delivered','cancelled') DEFAULT 'new',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    order_id    INT NOT NULL,
    product_id  INT NOT NULL,
    quantity    INT NOT NULL,
    price       DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin: admin@phonestore.ru / admin123
INSERT IGNORE INTO users (name, email, password, role) VALUES
('Administrator', 'admin@phonestore.ru', '$2y$10$WNrnZnjMRvO9OzF2rLlT2.4QLbwApJuRgHPK3LxzN5QYG0pnz/.d2', 'admin');

-- Demo user shown on login page: slava@gmail.com / 123456
INSERT IGNORE INTO users (name, email, password, role) VALUES
('Slava Demo', 'slava@gmail.com', '$2y$10$XQwgcU3vI9wIjNoQjhN.fellTIJoVFyi6ZrudGlPci9QlCxnQ9gAy', 'user');

INSERT IGNORE INTO products (id, name, brand, price, description, image, stock) VALUES
(1,  'iPhone 16 Pro',        'Apple',   129990, 'Titanium design, A18 Pro chip, 48MP camera system, Action button', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-finish-select-202409-6-9inch-deserttitanium?wid=400&hei=400&fmt=png-alpha', 50),
(2,  'iPhone 16',            'Apple',    89990, 'A18 chip, Dynamic Island, 48MP camera, Action button', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-finish-select-202409-6-1inch-pink?wid=400&hei=400&fmt=png-alpha', 80),
(3,  'iPhone 15',            'Apple',    74990, 'A16 Bionic, Dynamic Island, USB-C, 48MP main camera', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-finish-select-202309-6-1inch-pink?wid=400&hei=400&fmt=png-alpha', 60),
(4,  'Samsung Galaxy S25',   'Samsung',  99990, 'Snapdragon 8 Elite, 50MP camera, 4000mAh, One UI 7', 'https://images.samsung.com/is/image/samsung/p6pim/levant/2501/gallery/levant-galaxy-s25-s931-sm-s931bzadeub-544213-544213-thumb-539851430?$304_304_PNG$', 45),
(5,  'Samsung Galaxy S25+',  'Samsung', 119990, 'Snapdragon 8 Elite, 6.7-inch display, 45W charging', 'https://images.samsung.com/is/image/samsung/p6pim/levant/2501/gallery/levant-galaxy-s25-s936-sm-s936bzadeub-thumb-539851431?$304_304_PNG$', 30),
(6,  'Samsung Galaxy A55',   'Samsung',  39990, 'Exynos 1480, 50MP camera, 5000mAh, IP67', 'https://images.samsung.com/is/image/samsung/p6pim/ru/sm-a556ezaacis/gallery/ru-galaxy-a55-5g-sm-a556-sm-a556ezaacis-thumb-539809031?$304_304_PNG$', 90),
(7,  'Xiaomi 15',            'Xiaomi',   79990, 'Snapdragon 8 Elite, Leica optics, 5400mAh HyperCharge', 'https://i02.appmifile.com/mi-com-product/fly-birds/xiaomi-15/pc/xiaomi-15_ksp_thumb.png', 40),
(8,  'Xiaomi Redmi Note 13', 'Xiaomi',   24990, '200MP camera, 5000mAh, 67W fast charging, AMOLED 120Hz', 'https://i02.appmifile.com/mi-com-product/fly-birds/redmi-note-13/pc/thumbnail.png', 120),
(9,  'Huawei Pura 70 Pro',   'Huawei',   89990, 'Kirin 9010, Variable aperture, 100x zoom, HarmonyOS 4', 'https://consumer.huawei.com/content/dam/huawei-cbg-site/common/mkt/pdp/phones/pura70-pro/imgs/overview/kv/Pura70-Pro-kv.png', 25),
(10, 'Huawei Nova 12',       'Huawei',   34990, '60MP front camera, 100W charging, OLED display', 'https://consumer.huawei.com/content/dam/huawei-cbg-site/common/mkt/pdp/phones/nova12/img/overview/Nova12-kv-image.png', 55);

SELECT 'PhoneStore installed OK' AS message;
